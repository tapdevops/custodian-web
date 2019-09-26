START : 2018-09-27 14:35:23

            UPDATE TM_HO_COA
            SET 
                COA_CODE = '1101010301',
                COA_NAME = '',
                COA_GROUP = '',
                STATUS = 'Y',
                UPDATE_USER = 'MAGANG.CSI1',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3pAAeAAAxS9AAF';
        COMMIT;
END : 2018-09-27 14:35:23
