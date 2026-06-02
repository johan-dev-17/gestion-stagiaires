<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" encoding="UTF-8" indent="yes" />

  <xsl:template match="/">
    <html>
      <head>
        <title>Export XML</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&amp;display=swap" rel="stylesheet" />
        <style>
          body { 
            font-family: 'Inter', sans-serif; 
            background: #f3f6f9; 
            padding: 40px; 
            color: #0f172a; 
            margin: 0;
          }
          .container {
            max-width: 1200px;
            margin: 0 auto;
          }
          h1 { 
            font-weight: 800; 
            margin-bottom: 24px; 
            text-transform: capitalize; 
            color: #1e1b4b;
          }
          .table-wrapper {
            background: white; 
            border-radius: 16px; 
            overflow: hidden; 
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
          }
          table { 
            width: 100%; 
            border-collapse: collapse; 
          }
          th, td { 
            padding: 16px 24px; 
            text-align: left; 
            border-bottom: 1px solid #e2e8f0; 
          }
          th { 
            background: #4f46e5; 
            color: white; 
            font-weight: 600; 
            text-transform: uppercase; 
            font-size: 13px; 
            letter-spacing: 0.5px;
          }
          tr:hover { 
            background: #f8fafc; 
          }
          tr:last-child td {
            border-bottom: none;
          }
        </style>
      </head>
      <body>
        <div class="container">
          <h1>Export : <xsl:value-of select="name(/*)"/></h1>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <xsl:for-each select="/*/*[1]/*">
                    <th><xsl:value-of select="name()"/></th>
                  </xsl:for-each>
                </tr>
              </thead>
              <tbody>
                <xsl:for-each select="/*/*">
                  <tr>
                    <xsl:for-each select="*">
                      <td><xsl:value-of select="."/></td>
                    </xsl:for-each>
                  </tr>
                </xsl:for-each>
              </tbody>
            </table>
          </div>
        </div>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
