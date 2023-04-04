<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/">
        <html>
            <head>
                <title>Zpráva</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 16px;
                        line-height: 1.5;
                        margin: 0;
                        padding: 0;
                    }
                    h1 {
                        font-size: 24px;
                        margin: 20px 0;
                        text-align: center;
                    }
                    table {
                        border-collapse: collapse;
                        margin: 0 auto;
                        width: 100%;
                    }
                    th, td {
                        border: 1px solid #ccc;
                        padding: 10px;
                        text-align: left;
                        vertical-align: top;
                    }
                    th {
                        background-color: #f2f2f2;
                        font-weight: normal;
                    }
                </style>
            </head>
            <body>
                <h1>Zpráva</h1>
                <table>
                    <tr>
                        <th>Jméno</th>
                        <td><xsl:value-of select="zprava/jmeno"/></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><xsl:value-of select="zprava/email"/></td>
                    </tr>
                    <tr>
                        <th>Zpráva</th>
                        <td><xsl:value-of select="zprava/zprava"/></td>
                    </tr>
                </table>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
