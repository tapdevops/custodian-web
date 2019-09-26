START : 2018-08-02 10:07:41

                UPDATE TM_HO_RENCANA_KERJA 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00054',
                    CC_CODE = 'D00057',
                    RK_NAME = 'Pemenuhan License '||'&'||' ATSs',
                    RK_DESCRIPTION = 'Keterangan Pemenuhan License '||'&'||' ATS',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr7IAAeAAAxTuAAC';
            COMMIT;
END : 2018-08-02 10:07:42
