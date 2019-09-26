START : 2018-08-30 20:33:23

            UPDATE TM_HO_COA
            SET 
                COA_CODE = '7101010901',
                COA_NAME = 'Loan Adm Exp',
                COA_GROUP = 'OPEX HO, LOAD ADM',
                STATUS = 'Y',
                UPDATE_USER = 'MUHAMMAD.RIZALDY',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3pAAaAACal9AAU';
        COMMIT;
END : 2018-08-30 20:33:23
