START : 2018-08-29 15:09:03

            UPDATE TM_HO_COST_CENTER
            SET 
                HCC_CC = '067',
                HCC_DIVISI = 'D00011',
                HCC_COST_CENTER = 'CSR '||'&'||' PLASMA DIVISION - KALTIM',
                HCC_COST_CENTER_HEAD = 'JIMMY SURYO HADI',
                HCC_DIVISION_HEAD = 'LEFRAND HOSANG '||'&'||' SUHARIONO',
                UPDATE_USER = 'MUHAMMAD.RIZALDY',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3bAAeAAAxSPAAn';
        COMMIT;
END : 2018-08-29 15:09:04
