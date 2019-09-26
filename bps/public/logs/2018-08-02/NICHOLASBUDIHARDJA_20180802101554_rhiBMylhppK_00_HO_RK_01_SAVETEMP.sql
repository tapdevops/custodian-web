START : 2018-08-02 10:15:54

                UPDATE TM_HO_RENCANA_KERJA 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00054',
                    CC_CODE = 'D00057',
                    RK_NAME = 'Regular Stock Operational',
                    RK_DESCRIPTION = 'Keterangan Regular Stock Operationals',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr7IAAeAAAxTsAAA';
            COMMIT;
END : 2018-08-02 10:15:54
