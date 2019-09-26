START : 2018-07-23 13:49:26

            UPDATE TR_HO_SUMMARY_OUTLOOK
            SET
                REMARKS = 'testing',
                UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                UPDATE_TIME = CURRENT_TIMESTAMP
            WHERE
                EXTRACT (YEAR FROM PERIOD_BUDGET) = '01-JUL-18'
                AND CC_CODE = 'D00057'
                AND COA_CODE = '6201010102';
        
            UPDATE TR_HO_SUMMARY_OUTLOOK
            SET
                REMARKS = 'test',
                UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                UPDATE_TIME = CURRENT_TIMESTAMP
            WHERE
                EXTRACT (YEAR FROM PERIOD_BUDGET) = '01-JUL-18'
                AND CC_CODE = 'D00057'
                AND COA_CODE = '6201011101';
        COMMIT;
END : 2018-07-23 13:49:26
