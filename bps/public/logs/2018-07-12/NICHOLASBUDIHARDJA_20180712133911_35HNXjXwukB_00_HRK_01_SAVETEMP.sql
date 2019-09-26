START : 2018-07-12 13:39:11

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
                    'D00054',
                    'D00057',
                    'Cloud '||'&'||' CoLo Core 40% Non Core 60%',
                    'Keterangan Cloud '||'&'||' CoLo Core 40% Non Core 60%',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
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
                    'D00054',
                    'D00057',
                    'Pemenuhan License '||'&'||' ATS',
                    'Keterangan Pemenuhan License '||'&'||' ATS',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            COMMIT;
END : 2018-07-12 13:39:12
