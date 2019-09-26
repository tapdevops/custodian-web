START : 2018-08-13 11:05:24

            UPDATE TM_HO_DIVISION
            SET 
                DIV_CODE = 'D00001',
                DIV_NAME = 'CEOs',
                UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = '2';
        COMMIT;
END : 2018-08-13 11:05:35
