START : 2018-08-02 09:33:12

            UPDATE TM_HO_COST_CENTER
            SET 
                HCC_CC = 'D00004',
                HCC_DIVISI = 'D00004',
                HCC_COST_CENTER = 'COMMISSIONER',
                HCC_COST_CENTER_HEAD = 'COMM HEADssss',
                HCC_DIVISION_HEAD = 'abc',
                UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3bAAeAAAxSOAAA';
        COMMIT;
END : 2018-08-02 09:33:14
