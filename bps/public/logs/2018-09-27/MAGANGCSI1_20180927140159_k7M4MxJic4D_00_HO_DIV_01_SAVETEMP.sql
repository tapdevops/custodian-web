START : 2018-09-27 14:01:59

            UPDATE TM_HO_DIVISION
            SET 
                PERIOD_BUDGET = TO_DATE('2019', 'RRRR'),
                DIV_CODE = 'D00192',
                DIV_NAME = 'CORPORATE AUDIT DIVISION',
                UPDATE_USER = 'MAGANG.CSI1',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr5+AAeAAAxTeAAr';
        COMMIT;
END : 2018-09-27 14:01:59
