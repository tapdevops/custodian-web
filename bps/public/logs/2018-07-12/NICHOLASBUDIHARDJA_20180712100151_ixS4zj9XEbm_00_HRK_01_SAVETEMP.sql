START : 2018-07-12 10:01:51

                UPDATE TM_HO_RENCANA_KERJA 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00054',
                    CC_CODE = 'D00057',
                    RK_NAME = 'Regular Stock Operational',
                    RK_DESCRIPTION = 'Keterangan Regular Stock Operational',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr7IAAeAAAxTsAAA';
            
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
                    'Cloud &. CoLo Core 40% Non Core 60%',
                    'Keterangan Cloud &. CoLo Core 40% Non Core 60%',
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
                    'Consumable',
                    'Keterangan Consumable',
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
                    'Pemenuhan License &. ATS',
                    'Keterangan Pemenuhan License &. ATS',
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
                    'Regular Maintenance',
                    'Keterangan Regular Maintenance',
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
                    'Device Manage Service',
                    'Keterangan Device Manage Service',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            COMMIT;
END : 2018-07-12 10:01:53
