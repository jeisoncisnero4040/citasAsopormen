<?php


"CREATE TRIGGER anular_admision 
ON citas
AFTER UPDATE
AS
BEGIN
    IF UPDATE(cancelada)
    BEGIN
        /********* Encabezado pagosr *********/
        DECLARE @id             NUMERIC(18,0);
        DECLARE @nroadm         VARCHAR(10);
        DECLARE @usucana        VARCHAR(50);
        -- Req 38610: se agregan las variables yearfecha, monthfecha, nrocpt
        DECLARE @yearfecha      NUMERIC(18,0);
        DECLARE @monthfecha     NUMERIC(18,0);
        DECLARE @nrocpt         VARCHAR(50);
        DECLARE @autorizacion   VARCHAR(50);
        DECLARE @cod_prod       VARCHAR(10);
        DECLARE @cod_cli        VARCHAR(10);
        DECLARE @parameautoriza BIT;
        DECLARE @paramemultarifas BIT;

        SET @id = (
            SELECT id 
            FROM inserted
        );

        -- Req 36858: cambio en obtención de usu_can
        SET @usucana = (
            SELECT cedula
            FROM usuarios
            WHERE usuario = (
                SELECT usu_can 
                FROM citas 
                WHERE id = @id
            )
        );

        SET @cod_cli = (
            SELECT nro_hist 
            FROM citas 
            WHERE id = @id
        );

        SET @cod_prod = (
            SELECT tiempo 
            FROM citas 
            WHERE id = @id
        );

        SET @autorizacion = (
            SELECT autoriz 
            FROM citas 
            WHERE id = @id
        );

        SET @paramemultarifas = (
            SELECT multiplestarifas 
            FROM mod_administra
        );

        SET @parameautoriza = (
            SELECT rautoriza 
            FROM Parame
        );

        /********* Fin encabezado *********/

        IF EXISTS (
            SELECT recibo 
            FROM pagosr 
            WHERE idcita = @id 
              AND usu_anul = ''
        )
        BEGIN
            IF @parameautoriza = 1
            BEGIN
                IF EXISTS (
                    SELECT procedi
                    FROM autorizad
                    WHERE LTRIM(historia) = @cod_cli
                      AND LTRIM(n_autoriza) = @autorizacion
                      AND LTRIM(procedi) = @cod_prod
                )
                BEGIN
                    SET @nroadm = (
                        SELECT recibo 
                        FROM pagosr 
                        WHERE idcita = @id 
                          AND usu_anul = ''
                    );

                    SET @yearfecha = (
                        SELECT YEAR(fecha) 
                        FROM pagosr 
                        WHERE idcita = @id 
                          AND usu_anul = ''
                    );
                END
            END
        END
    END
END
GO
";