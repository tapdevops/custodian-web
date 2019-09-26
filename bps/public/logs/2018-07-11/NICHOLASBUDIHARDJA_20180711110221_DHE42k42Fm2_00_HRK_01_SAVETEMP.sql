START : 2018-07-11 11:02:21

                UPDATE TM_HO_RENCANA_KERJA 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00054',
                    CC_CODE = 'D00057',
                    RK_NAME = 'cd',
                    RK_DESCRIPTION = 'd',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr7IAAeAAAxTsAAB';
            
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
                    'z',
                    'z',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            COMMIT;
END : 2018-07-11 11:02:22
