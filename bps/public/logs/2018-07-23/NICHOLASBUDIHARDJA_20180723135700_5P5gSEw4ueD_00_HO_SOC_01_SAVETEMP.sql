START : 2018-07-23 13:57:00

            UPDATE TR_HO_SUMMARY_OUTLOOK
            SET
                REMARKS = 'TEST',
                UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                UPDATE_TIME = CURRENT_TIMESTAMP
            WHERE
                EXTRACT (YEAR FROM PERIOD_BUDGET) = '2018'
                AND CC_CODE = 'D00057'
                AND COA_CODE = '6201010102';
        
            UPDATE TR_HO_SUMMARY_OUTLOOK
            SET
                REMARKS = 'TESTING',
                UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                UPDATE_TIME = CURRENT_TIMESTAMP
            WHERE
                EXTRACT (YEAR FROM PERIOD_BUDGET) = '2018'
                AND CC_CODE = 'D00057'
                AND COA_CODE = '6201011101';
        COMMIT;
END : 2018-07-23 13:57:00
