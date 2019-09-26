START : 2018-08-29 10:54:52

            UPDATE TM_HO_DIVISION
            SET 
                PERIOD_BUDGET = TO_DATE('2018', 'RRRR'),
                DIV_CODE = 'D00162',
                DIV_NAME = 'HOLDING DEPARTMENT',
                UPDATE_USER = 'MUHAMMAD.RIZALDY',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr5+AAeAAAxTeAAG';
        COMMIT;
END : 2018-08-29 10:54:52
