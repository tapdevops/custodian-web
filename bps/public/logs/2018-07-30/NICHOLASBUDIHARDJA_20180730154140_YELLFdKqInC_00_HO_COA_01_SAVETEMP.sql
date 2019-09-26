START : 2018-07-30 15:41:40

            UPDATE TM_HO_COA
            SET 
                COA_CODE = '6201011201',
                COA_NAME = 'G&A - Relationship',
                COA_GROUP = 'OPEX HO',
                STATUS = 'Y',
                UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3pAAeAAAxS7AAC';
        COMMIT;
END : 2018-07-30 15:41:41
