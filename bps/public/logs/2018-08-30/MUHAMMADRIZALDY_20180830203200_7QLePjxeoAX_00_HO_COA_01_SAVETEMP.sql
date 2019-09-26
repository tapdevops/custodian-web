START : 2018-08-30 20:32:00

            UPDATE TM_HO_COA
            SET 
                COA_CODE = '7101010401',
                COA_NAME = 'Bank Adm Exp',
                COA_GROUP = 'OPEX HO, BANK ADM',
                STATUS = 'Y',
                UPDATE_USER = 'MUHAMMAD.RIZALDY',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3pAAaAACal9AAP';
        COMMIT;
END : 2018-08-30 20:32:00
