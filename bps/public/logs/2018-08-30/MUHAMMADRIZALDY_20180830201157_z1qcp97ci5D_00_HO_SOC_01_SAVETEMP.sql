START : 2018-08-30 20:11:57

            UPDATE TR_HO_SUMMARY_OUTLOOK
            SET
                REMARKS = 'A',
                UPDATE_USER = 'MUHAMMAD.RIZALDY',
                UPDATE_TIME = CURRENT_TIMESTAMP
            WHERE
                EXTRACT (YEAR FROM PERIOD_BUDGET) = '2018'
                AND CC_CODE = '005'
                AND COA_CODE = '6201011101';
        COMMIT;
END : 2018-08-30 20:11:58
