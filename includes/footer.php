        </div>
    </main>
</div>
<div id="toast-container"></div>
<div id="modal-root"></div>
<script src="<?= strpos($_SERVER['PHP_SELF'], '/pages/') !== false ? '../assets/js/app.js' : 'assets/js/app.js' ?>"></script>
<?php if (!empty($pageScript)) echo $pageScript; ?>
</body>
</html>
