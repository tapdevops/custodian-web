START : 2018-09-10 21:13:34

            UPDATE TM_HO_COA
            SET 
                COA_CODE = '7101010501',
                COA_NAME = 'Interest Exp - Banks',
                COA_GROUP = 'OPEX HO',
                STATUS = 'Y',
                UPDATE_USER = 'MUHAMMAD.RIZALDY',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3pAAaAACal9AAQ';
        COMMIT;
END : 2018-09-10 21:13:35
