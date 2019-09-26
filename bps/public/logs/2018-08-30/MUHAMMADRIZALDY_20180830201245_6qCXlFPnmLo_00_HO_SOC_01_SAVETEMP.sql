START : 2018-08-30 20:12:45

            UPDATE TR_HO_SUMMARY_OUTLOOK
            SET
                REMARKS = 'Pelepasan Kawasan, AMDAL Untuk HGU, KADASTRAL dan HGU.',
                UPDATE_USER = 'MUHAMMAD.RIZALDY',
                UPDATE_TIME = CURRENT_TIMESTAMP
            WHERE
                EXTRACT (YEAR FROM PERIOD_BUDGET) = '2018'
                AND CC_CODE = '005'
                AND COA_CODE = '5403010105';
        COMMIT;
END : 2018-08-30 20:12:45
