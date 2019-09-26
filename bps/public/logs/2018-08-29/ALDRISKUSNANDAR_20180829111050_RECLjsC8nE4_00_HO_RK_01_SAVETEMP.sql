START : 2018-08-29 11:10:50

                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    '',
                    'D00001',
                    'Pembelian Unit Kendaraan',
                    'Kendaraan Operasional Baru ',
                    'ALDRIS.KUSNANDAR',
                    SYSDATE
                );
            COMMIT;
END : 2018-08-29 11:10:51
