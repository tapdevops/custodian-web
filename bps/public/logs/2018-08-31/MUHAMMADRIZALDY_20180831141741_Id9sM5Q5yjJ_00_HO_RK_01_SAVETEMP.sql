START : 2018-08-31 14:17:41

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
                    '009',
                    'free text',
                    'penjelasan',
                    'MUHAMMAD.RIZALDY',
                    SYSDATE
                );
            COMMIT;
END : 2018-08-31 14:17:41