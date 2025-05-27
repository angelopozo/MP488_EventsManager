<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="html" indent="yes" encoding="UTF-8" />

  <xsl:template match="/">
    <html>
      <head>
        <title>Resumen Económico Web</title>
        <link rel="stylesheet" type="text/css" href="styles.css" />
      </head>
      <body class="container">
        <h1>Resumen por tipo de ingreso</h1>
        <table border="1">
          <tr>
            <th>Tipo</th>
            <th>Total</th>
          </tr>
          <xsl:for-each-group select="ingresos/ingreso" group-by="@tipo">
            <tr>
              <td>
                <xsl:value-of select="current-grouping-key()" />
              </td>
              <td>
                <xsl:value-of select="format-number(sum(current-group()/monto), '#,##0.00')" />
                <xsl:text> €</xsl:text>
              </td>
            </tr>
          </xsl:for-each-group>

          <!-- Fila de total general -->
          <tr style="font-weight:bold; background-color:#e0e0e0;">
            <td>Total General</td>
            <td>
              <xsl:value-of select="format-number(sum(ingresos/ingreso/monto), '#,##0.00')" />
              <xsl:text> €</xsl:text>
            </td>
          </tr>
        </table>

        <xsl:for-each-group select="ingresos/ingreso" group-by="@tipo">
          <h2>
            <xsl:value-of select="current-grouping-key()" />
          </h2>
          <table border="1">
            <tr>
              <th>Fecha</th>
              <th>Monto</th>
              <th>Descripción</th>
            </tr>
            <xsl:for-each select="current-group()">
              <tr>
                <td>
                  <xsl:value-of select="fecha" />
                </td>
                <td>
                  <xsl:value-of select="format-number(monto, '#,##0.00')" />
                  <xsl:text> €</xsl:text>
                </td>
                <td>
                  <xsl:value-of select="descripcion" />
                </td>
              </tr>
            </xsl:for-each>
          </table>
        </xsl:for-each-group>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>