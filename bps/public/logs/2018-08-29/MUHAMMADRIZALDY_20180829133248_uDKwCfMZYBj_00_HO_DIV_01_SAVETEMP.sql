START : 2018-08-29 13:32:48

            UPDATE TM_HO_DIVISION
            SET 
                PERIOD_BUDGET = TO_DATE('2018', 'RRRR'),
                DIV_CODE = 'D00999',
                DIV_NAME = 'COMMISSIONER',
                UPDATE_USER = 'MUHAMMAD.RIZALDY',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr5+AAeAAAxTeAAA';
        COMMIT;
END : 2018-08-29 13:32:48
