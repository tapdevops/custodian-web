START : 2018-08-30 20:07:20

            UPDATE TM_HO_COA
            SET 
                COA_CODE = '1210010101',
                COA_NAME = 'Intangible Assets - At Cost',
                COA_GROUP = 'CAPEX, SPD',
                STATUS = 'Y',
                UPDATE_USER = 'MUHAMMAD.RIZALDY',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3pAAeAAAxS/ABQ';
        COMMIT;
END : 2018-08-30 20:07:20
