<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/xml_handler.php';

/**
 * Gestionnaire hybride MySQL + XML
 * MySQL : Stockage principal (lecture/écriture)
 * XML : Backup et synchronisation
 */
class HybridHandler {
    private $xmlHandler;
    private $table;
    private $db;

    public function __construct($xmlFile, $rootName, $itemName, $table) {
        $this->xmlHandler = new XMLHandler($xmlFile, $rootName, $itemName);
        $this->table = $table;
        $this->db = getDB();
    }

    public function all() {
        // Lire depuis MySQL
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table}");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Fallback vers XML si MySQL échoue
            error_log("MySQL error, falling back to XML: " . $e->getMessage());
            return $this->xmlHandler->all();
        }
    }

    public function find($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            if ($result) {
                return $result;
            }
            return null;
        } catch (PDOException $e) {
            error_log("MySQL error, falling back to XML: " . $e->getMessage());
            return $this->xmlHandler->find($id);
        }
    }

    public function add($data) {
        $this->db->beginTransaction();
        try {
            // Construire la requête INSERT dynamique
            $columns = array_keys($data);
            $placeholders = array_fill(0, count($columns), '?');
            
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_values($data));
            
            $id = $this->db->lastInsertId();
            
            // Ajouter aussi dans XML pour backup
            $data['id'] = $id;
            $this->xmlHandler->add($data);
            
            $this->db->commit();
            return $id;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $data) {
        $this->db->beginTransaction();
        try {
            // Construire la requête UPDATE dynamique
            $setParts = [];
            foreach (array_keys($data) as $column) {
                if ($column !== 'id') {
                    $setParts[] = "$column = ?";
                }
            }
            
            $values = array_values(array_filter($data, function($k) { return $k !== 'id'; }, ARRAY_FILTER_USE_KEY));
            $values[] = $id;
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            // Mettre à jour aussi dans XML pour backup
            $this->xmlHandler->update($id, $data);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            
            // Supprimer aussi dans XML pour backup
            $this->xmlHandler->delete($id);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Synchroniser depuis XML vers MySQL
    public function syncFromXml() {
        $items = $this->xmlHandler->all();
        $count = 0;
        
        foreach ($items as $item) {
            try {
                // Vérifier si l'item existe déjà
                $existing = $this->find($item['id']);
                if (!$existing) {
                    $this->add($item);
                    $count++;
                }
            } catch (Exception $e) {
                error_log("Sync error for item {$item['id']}: " . $e->getMessage());
            }
        }
        
        return $count;
    }

    // Synchroniser depuis MySQL vers XML
    public function syncToXml() {
        $items = $this->all();
        $count = 0;
        
        foreach ($items as $item) {
            try {
                $existing = $this->xmlHandler->find($item['id']);
                if ($existing) {
                    $this->xmlHandler->update($item['id'], $item);
                } else {
                    $this->xmlHandler->add($item);
                }
                $count++;
            } catch (Exception $e) {
                error_log("Sync error for item {$item['id']}: " . $e->getMessage());
            }
        }
        
        return $count;
    }
}
