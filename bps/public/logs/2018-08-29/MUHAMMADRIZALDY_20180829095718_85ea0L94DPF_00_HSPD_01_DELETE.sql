START : 2018-08-29 09:57:18

            UPDATE TR_HO_SPD
            SET DELETE_USER = 'MUHAMMAD.RIZALDY',
                DELETE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAsHlAAaAACaldAAA';
        COMMIT;
END : 2018-08-29 09:57:18