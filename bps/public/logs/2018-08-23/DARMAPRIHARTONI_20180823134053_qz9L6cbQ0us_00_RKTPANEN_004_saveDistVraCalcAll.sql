START : 2018-08-23 13:40:53

      DELETE FROM TR_RKT_VRA_DISTRIBUSI
      WHERE PERIOD_BUDGET = TO_DATE('01-01-2018','DD-MM-RRRR')
        AND BA_CODE = '2121' 
        AND ACTIVITY_CODE IN ('51800','51600')
        AND LOCATION_CODE = 'A';
    
      INSERT INTO TR_RKT_VRA_DISTRIBUSI(
        PERIOD_BUDGET, 
        BA_CODE, 
        ACTIVITY_CODE, 
        VRA_CODE, 
        LOCATION_CODE, 
        HM_KM, 
        PRICE_QTY_VRA, 
        PRICE_HM_KM, 
        TRX_CODE, 
        TIPE_TRANSAKSI, 
        INSERT_USER, 
        INSERT_TIME
      )
      VALUES(
        TO_DATE('01-01-2018','DD-MM-RRRR'),
        '2121', 
        '51800', 
        'DT010',
        'A', 
        0, 
        6030.0787, 
        0,
        '2018212151800DT010', 
        'INFRA', 
        'DARMA.PRIHARTONI', 
        SYSDATE
      );
    
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS010',
          'A', 
          0, 
          30601.1409, 
          0,
          '2018212151600FS010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS020',
          'A', 
          0, 
          0, 
          0,
          '2018212151600FS020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT010',
          'A', 
          0, 
          72952.0589, 
          0,
          '2018212151600FT010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT020',
          'A', 
          0, 
          0, 
          0,
          '2018212151600FT020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'TR010',
          'A', 
          0, 
          2148.2228, 
          0,
          '2018212151600TR010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
      DELETE FROM TR_RKT_VRA_DISTRIBUSI
      WHERE PERIOD_BUDGET = TO_DATE('01-01-2018','DD-MM-RRRR')
        AND BA_CODE = '2121' 
        AND ACTIVITY_CODE IN ('51800','51600')
        AND LOCATION_CODE = 'B';
    
      INSERT INTO TR_RKT_VRA_DISTRIBUSI(
        PERIOD_BUDGET, 
        BA_CODE, 
        ACTIVITY_CODE, 
        VRA_CODE, 
        LOCATION_CODE, 
        HM_KM, 
        PRICE_QTY_VRA, 
        PRICE_HM_KM, 
        TRX_CODE, 
        TIPE_TRANSAKSI, 
        INSERT_USER, 
        INSERT_TIME
      )
      VALUES(
        TO_DATE('01-01-2018','DD-MM-RRRR'),
        '2121', 
        '51800', 
        'DT010',
        'B', 
        29197.92043598, 
        6030.0787, 
        176065758.1053,
        '2018212151800DT010', 
        'INFRA', 
        'DARMA.PRIHARTONI', 
        SYSDATE
      );
    
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS010',
          'B', 
          260.61978, 
          30601.1409, 
          7975262.609107,
          '2018212151600FS010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS020',
          'B', 
          260.61978, 
          0, 
          0,
          '2018212151600FS020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT010',
          'B', 
          0, 
          72952.0589, 
          0,
          '2018212151600FT010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT020',
          'B', 
          0, 
          0, 
          0,
          '2018212151600FT020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'TR010',
          'B', 
          0, 
          2148.2228, 
          0,
          '2018212151600TR010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
      DELETE FROM TR_RKT_VRA_DISTRIBUSI
      WHERE PERIOD_BUDGET = TO_DATE('01-01-2018','DD-MM-RRRR')
        AND BA_CODE = '2121' 
        AND ACTIVITY_CODE IN ('51800','51600')
        AND LOCATION_CODE = 'C';
    
      INSERT INTO TR_RKT_VRA_DISTRIBUSI(
        PERIOD_BUDGET, 
        BA_CODE, 
        ACTIVITY_CODE, 
        VRA_CODE, 
        LOCATION_CODE, 
        HM_KM, 
        PRICE_QTY_VRA, 
        PRICE_HM_KM, 
        TRX_CODE, 
        TIPE_TRANSAKSI, 
        INSERT_USER, 
        INSERT_TIME
      )
      VALUES(
        TO_DATE('01-01-2018','DD-MM-RRRR'),
        '2121', 
        '51800', 
        'DT010',
        'C', 
        0, 
        6030.0787, 
        0,
        '2018212151800DT010', 
        'INFRA', 
        'DARMA.PRIHARTONI', 
        SYSDATE
      );
    
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS010',
          'C', 
          0, 
          30601.1409, 
          0,
          '2018212151600FS010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS020',
          'C', 
          0, 
          0, 
          0,
          '2018212151600FS020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT010',
          'C', 
          0, 
          72952.0589, 
          0,
          '2018212151600FT010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT020',
          'C', 
          0, 
          0, 
          0,
          '2018212151600FT020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'TR010',
          'C', 
          0, 
          2148.2228, 
          0,
          '2018212151600TR010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
      DELETE FROM TR_RKT_VRA_DISTRIBUSI
      WHERE PERIOD_BUDGET = TO_DATE('01-01-2018','DD-MM-RRRR')
        AND BA_CODE = '2121' 
        AND ACTIVITY_CODE IN ('51800','51600')
        AND LOCATION_CODE = 'D';
    
      INSERT INTO TR_RKT_VRA_DISTRIBUSI(
        PERIOD_BUDGET, 
        BA_CODE, 
        ACTIVITY_CODE, 
        VRA_CODE, 
        LOCATION_CODE, 
        HM_KM, 
        PRICE_QTY_VRA, 
        PRICE_HM_KM, 
        TRX_CODE, 
        TIPE_TRANSAKSI, 
        INSERT_USER, 
        INSERT_TIME
      )
      VALUES(
        TO_DATE('01-01-2018','DD-MM-RRRR'),
        '2121', 
        '51800', 
        'DT010',
        'D', 
        0, 
        6030.0787, 
        0,
        '2018212151800DT010', 
        'INFRA', 
        'DARMA.PRIHARTONI', 
        SYSDATE
      );
    
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS010',
          'D', 
          0, 
          30601.1409, 
          0,
          '2018212151600FS010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS020',
          'D', 
          0, 
          0, 
          0,
          '2018212151600FS020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT010',
          'D', 
          0, 
          72952.0589, 
          0,
          '2018212151600FT010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT020',
          'D', 
          0, 
          0, 
          0,
          '2018212151600FT020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'TR010',
          'D', 
          0, 
          2148.2228, 
          0,
          '2018212151600TR010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
      DELETE FROM TR_RKT_VRA_DISTRIBUSI
      WHERE PERIOD_BUDGET = TO_DATE('01-01-2018','DD-MM-RRRR')
        AND BA_CODE = '2121' 
        AND ACTIVITY_CODE IN ('51800','51600')
        AND LOCATION_CODE = 'E';
    
      INSERT INTO TR_RKT_VRA_DISTRIBUSI(
        PERIOD_BUDGET, 
        BA_CODE, 
        ACTIVITY_CODE, 
        VRA_CODE, 
        LOCATION_CODE, 
        HM_KM, 
        PRICE_QTY_VRA, 
        PRICE_HM_KM, 
        TRX_CODE, 
        TIPE_TRANSAKSI, 
        INSERT_USER, 
        INSERT_TIME
      )
      VALUES(
        TO_DATE('01-01-2018','DD-MM-RRRR'),
        '2121', 
        '51800', 
        'DT010',
        'E', 
        137837.82747205, 
        6030.0787, 
        831172947.4935,
        '2018212151800DT010', 
        'INFRA', 
        'DARMA.PRIHARTONI', 
        SYSDATE
      );
    
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS010',
          'E', 
          182.19602, 
          30601.1409, 
          5575406.0794392,
          '2018212151600FS010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS020',
          'E', 
          182.19602, 
          0, 
          0,
          '2018212151600FS020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT010',
          'E', 
          0, 
          72952.0589, 
          0,
          '2018212151600FT010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT020',
          'E', 
          0, 
          0, 
          0,
          '2018212151600FT020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'TR010',
          'E', 
          0, 
          2148.2228, 
          0,
          '2018212151600TR010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
      DELETE FROM TR_RKT_VRA_DISTRIBUSI
      WHERE PERIOD_BUDGET = TO_DATE('01-01-2018','DD-MM-RRRR')
        AND BA_CODE = '2121' 
        AND ACTIVITY_CODE IN ('51800','51600')
        AND LOCATION_CODE = 'F';
    
      INSERT INTO TR_RKT_VRA_DISTRIBUSI(
        PERIOD_BUDGET, 
        BA_CODE, 
        ACTIVITY_CODE, 
        VRA_CODE, 
        LOCATION_CODE, 
        HM_KM, 
        PRICE_QTY_VRA, 
        PRICE_HM_KM, 
        TRX_CODE, 
        TIPE_TRANSAKSI, 
        INSERT_USER, 
        INSERT_TIME
      )
      VALUES(
        TO_DATE('01-01-2018','DD-MM-RRRR'),
        '2121', 
        '51800', 
        'DT010',
        'F', 
        5481.1170136967, 
        6030.0787, 
        33051566.9565,
        '2018212151800DT010', 
        'INFRA', 
        'DARMA.PRIHARTONI', 
        SYSDATE
      );
    
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS010',
          'F', 
          87.65678, 
          30601.1409, 
          2682397.4756203,
          '2018212151600FS010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS020',
          'F', 
          87.65678, 
          0, 
          0,
          '2018212151600FS020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT010',
          'F', 
          0, 
          72952.0589, 
          0,
          '2018212151600FT010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT020',
          'F', 
          0, 
          0, 
          0,
          '2018212151600FT020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'TR010',
          'F', 
          0, 
          2148.2228, 
          0,
          '2018212151600TR010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
      DELETE FROM TR_RKT_VRA_DISTRIBUSI
      WHERE PERIOD_BUDGET = TO_DATE('01-01-2018','DD-MM-RRRR')
        AND BA_CODE = '2121' 
        AND ACTIVITY_CODE IN ('51800','51600')
        AND LOCATION_CODE = 'G';
    
      INSERT INTO TR_RKT_VRA_DISTRIBUSI(
        PERIOD_BUDGET, 
        BA_CODE, 
        ACTIVITY_CODE, 
        VRA_CODE, 
        LOCATION_CODE, 
        HM_KM, 
        PRICE_QTY_VRA, 
        PRICE_HM_KM, 
        TRX_CODE, 
        TIPE_TRANSAKSI, 
        INSERT_USER, 
        INSERT_TIME
      )
      VALUES(
        TO_DATE('01-01-2018','DD-MM-RRRR'),
        '2121', 
        '51800', 
        'DT010',
        'G', 
        56428.026874707, 
        6030.0787, 
        340265442.9402,
        '2018212151800DT010', 
        'INFRA', 
        'DARMA.PRIHARTONI', 
        SYSDATE
      );
    
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS010',
          'G', 
          230.20124, 
          30601.1409, 
          7044420.5805947,
          '2018212151600FS010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS020',
          'G', 
          230.20124, 
          0, 
          0,
          '2018212151600FS020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT010',
          'G', 
          0, 
          72952.0589, 
          0,
          '2018212151600FT010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT020',
          'G', 
          0, 
          0, 
          0,
          '2018212151600FT020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'TR010',
          'G', 
          0, 
          2148.2228, 
          0,
          '2018212151600TR010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
      DELETE FROM TR_RKT_VRA_DISTRIBUSI
      WHERE PERIOD_BUDGET = TO_DATE('01-01-2018','DD-MM-RRRR')
        AND BA_CODE = '2121' 
        AND ACTIVITY_CODE IN ('51800','51600')
        AND LOCATION_CODE = 'H';
    
      INSERT INTO TR_RKT_VRA_DISTRIBUSI(
        PERIOD_BUDGET, 
        BA_CODE, 
        ACTIVITY_CODE, 
        VRA_CODE, 
        LOCATION_CODE, 
        HM_KM, 
        PRICE_QTY_VRA, 
        PRICE_HM_KM, 
        TRX_CODE, 
        TIPE_TRANSAKSI, 
        INSERT_USER, 
        INSERT_TIME
      )
      VALUES(
        TO_DATE('01-01-2018','DD-MM-RRRR'),
        '2121', 
        '51800', 
        'DT010',
        'H', 
        46541.139732521, 
        6030.0787, 
        280646735.3748,
        '2018212151800DT010', 
        'INFRA', 
        'DARMA.PRIHARTONI', 
        SYSDATE
      );
    
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS010',
          'H', 
          343.6355, 
          30601.1409, 
          10515638.353742,
          '2018212151600FS010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FS020',
          'H', 
          343.6355, 
          0, 
          0,
          '2018212151600FS020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT010',
          'H', 
          0, 
          72952.0589, 
          0,
          '2018212151600FT010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'FT020',
          'H', 
          0, 
          0, 
          0,
          '2018212151600FT020', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-2018','DD-MM-RRRR'),
          '2121', 
          '51600', 
          'TR010',
          'H', 
          0, 
          2148.2228, 
          0,
          '2018212151600TR010', 
          'INFRA', 
          'DARMA.PRIHARTONI', 
          SYSDATE
        );
      COMMIT;
END : 2018-08-23 13:40:54
