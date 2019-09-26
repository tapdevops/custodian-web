START : 2018-08-07 10:14:05

                UPDATE TM_HO_RENCANA_KERJA 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00054',
                    CC_CODE = 'D00058',
                    RK_NAME = 'Tambah',
                    RK_DESCRIPTION = 'sTambah',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr7IAAeAAAxTsAAD';
            COMMIT;
END : 2018-08-07 10:14:05
