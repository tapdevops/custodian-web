START : 2018-08-30 20:11:09

            UPDATE TR_HO_SUMMARY_OUTLOOK
            SET
                REMARKS = 'Biaya Anggota Baru di Kementrian',
                UPDATE_USER = 'MUHAMMAD.RIZALDY',
                UPDATE_TIME = CURRENT_TIMESTAMP
            WHERE
                EXTRACT (YEAR FROM PERIOD_BUDGET) = '2018'
                AND CC_CODE = '005'
                AND COA_CODE = '6201011503';
        COMMIT;
END : 2018-08-30 20:11:09
