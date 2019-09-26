START : 2018-08-14 11:07:46

            UPDATE TM_HO_DIVISION
            SET 
                DIV_CODE = 'TTT',
                DIV_NAME = 'TESTING & TESTS',
                UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr5+AAeAAAxTdAAA';
        COMMIT;
END : 2018-08-14 11:07:47
