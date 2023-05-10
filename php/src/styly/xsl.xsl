<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    
    <xsl:template match="/">
        <html>
            <head>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"></link>
                <title>School Information</title>
            </head>
            <body>
                <h1 class="d-flex justify-content-center"><xsl:value-of select="school/school_name"/></h1>
                <p class="d-flex justify-content-center" ><xsl:value-of select="school/fullname"/></p>
                <h2 class="ml-1">Lokace</h2>
                <ul class="ml-1">
                    <li>Stát: <xsl:value-of select="school/location/country"/></li>
                    <li>Město: <xsl:value-of select="school/location/city"/></li>
                    <li>PSČ: <xsl:value-of select="school/location/city/@zip"/></li>
                    <li>Adresa: <xsl:value-of select="school/location/address"/></li>
                </ul>
                <h2 class="ml-1">Fakulty</h2>
                    <xsl:for-each select="school/faculties/faculty">
                        <ul>
                        <li class="ml-2"><xsl:value-of select="f_name"/> (<xsl:value-of select="f_name/@shortcut"/>)
                            <xsl:if test="katedry">
                                <ul>
                                    <li class="ml-2">Katedry:</li>
                                        <ul>
                                            <xsl:for-each select="katedry/katedra">
                                                <li class="ml-2"><xsl:value-of select="k_name"/>
                                                    <xsl:if test="teachers/teacher">
                                                        <ul>
                                                            <li class="ml-3">Vyučující:
                                                                <ul>
                                                                <xsl:for-each select="teachers/teacher">
                                                                    <li class="ml-4"><xsl:value-of select="t_name"/> - <xsl:value-of select="pozice"/> (<xsl:value-of select="t_telefon"/>)</li>
                                                                </xsl:for-each>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                    </xsl:if>
                                                    <xsl:if test="students/student">
                                                        <ul>
                                                            <li class="ml-3">Studenti:
                                                            <ul>
                                                                <xsl:for-each select="students/student">
                                                                    <li class="ml-4"><xsl:value-of select="s_name"/> - <xsl:value-of select="st"/> (<xsl:value-of select="s_email"/>)</li>
                                                                </xsl:for-each>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                    </xsl:if>
                                                </li>
                                            </xsl:for-each>
                                        </ul>
                                    </ul>
                                </xsl:if>
                            </li>
                        </ul>
                    </xsl:for-each>
            </body>
        </html>
    </xsl:template>
    
</xsl:stylesheet>
