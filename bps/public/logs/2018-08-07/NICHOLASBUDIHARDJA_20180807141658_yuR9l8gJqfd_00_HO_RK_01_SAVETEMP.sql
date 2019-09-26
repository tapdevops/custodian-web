START : 2018-08-07 14:16:58

                UPDATE TM_HO_RENCANA_KERJA 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00054',
                    CC_CODE = 'D00057',
                    RK_NAME = 'Regular Stock Operational',
                    RK_DESCRIPTION = 'Keterangan Regular Stock Operational',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr7IAAeAAAxTsAAA';
            
                UPDATE TM_HO_RENCANA_KERJA 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00054',
                    CC_CODE = 'D00057',
                    RK_NAME = 'Cloud '||'&'||' CoLo Core 40% Non Core 60%',
                    RK_DESCRIPTION = 'Keterangan Cloud '||'&'||' CoLo Core 40% Non Core 60%',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr7IAAeAAAxTtAAD';
            
                UPDATE TM_HO_RENCANA_KERJA 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00054',
                    CC_CODE = 'D00057',
                    RK_NAME = 'Consumable',
                    RK_DESCRIPTION = 'Keterangan Consumable',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr7IAAeAAAxTtAAF';
            
                UPDATE TM_HO_RENCANA_KERJA 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00054',
                    CC_CODE = 'D00057',
                    RK_NAME = 'Pemenuhan License '||'&'||' ATS',
                    RK_DESCRIPTION = 'Keterangan Pemenuhan License '||'&'||' ATS',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr7IAAeAAAxTtAAG';
            
                UPDATE TM_HO_RENCANA_KERJA 
                SET 
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00054',
                    CC_CODE = 'D00057',
                    RK_NAME = 'Regular Maintenance (Device)',
                    RK_DESCRIPTION = 'Keterangan Regular Maintenance (Device)',
                    UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAr7IAAeAAAxTuAAB';
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Device Manage Service',
                    'Keterangan Device Manage Service',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Employee Manage Service',
                    'Keterangan Employee Manage Service',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Biaya Jaringan Site (Existing)',
                    'Keterangan Biaya Jaringan Site (Existing)',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Pemenuhan Jaringan Site (Fulfillment)',
                    'Keterangan Pemenuhan Jaringan Site (Fulfillment)',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Regular Maintenance (Jaringan)',
                    'Keterangan Regular Maintenance (Jaringan)',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Regular Maintenance (Tower)',
                    'Keterangan Regular Maintenance (Tower)',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Network Traffic Improvement (Compression)',
                    'Keterangan Network Traffic Improvement (Compression)',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Perpanjangan Domain',
                    'Keterangan Perpanjangan Domain',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Pemeliharaan License dan ATS (sudah ada)',
                    'Keterangan Pemeliharaan License dan ATS (sudah ada)',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Pengembangan Jaringan SITE',
                    'Keterangan Pengembangan Jaringan SITE',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Biaya Komunikasi Site (Existing)',
                    'Keterangan Biaya Komunikasi Site (Existing)',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Annual Maintenance Datacenter HO',
                    'Keterangan Annual Maintenance Datacenter HO',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Supporting Datacenter HO',
                    'Keterangan Supporting Datacenter HO',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'SAP License '||'&'||' Support',
                    'Keterangan SAP License '||'&'||' Support',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Radio HT Communication Improvement',
                    'Keterangan Radio HT Communication Improvement',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Device Fullfilment',
                    'Keterangan Device Fullfilment',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Rollout Project',
                    'Keterangan Rollout Project',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'TAP View',
                    'Keterangan TAP View',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Backup Devices',
                    'Keterangan Backup Devices',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'New Project',
                    'Keterangan New Project',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'GIS Support',
                    'Keterangan GIS Support',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Datacenter HO Improvement',
                    'Keterangan Datacenter HO Improvement',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Operational Backup',
                    'Keterangan Operational Backup',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Regular Datacenter Operation HO',
                    'Keterangan Regular Datacenter Operation HO',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Data Availability Improvement',
                    'Keterangan Data Availability Improvement',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'New License',
                    'Keterangan New License',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Daily Visual Control Maps Project',
                    'Keterangan Daily Visual Control Maps Project',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Regular Stock Operational (Jaringan)',
                    'Keterangan Regular Stock Operational (Jaringan)',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Pengembangan Jaringan '||'&'||' Security di HO',
                    'Keterangan Pengembangan Jaringan '||'&'||' Security di HO',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'ATS License Cost',
                    'Keterangan ATS License Cost',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Production Forecast (Enhancement)',
                    'Keterangan Production Forecast (Enhancement)',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Dashboard Information Estate Management',
                    'Keterangan Dashboard Information Estate Management',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Dashboard Daily Site',
                    'Keterangan Dashboard Daily Site',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Premi Automation Transport',
                    'Keterangan Premi Automation Transport',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Enhancement '||'&'||' Support Aplication',
                    'Keterangan Enhancement '||'&'||' Support Aplication',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Data Engineer for Data Warehouse',
                    'Keterangan Data Engineer for Data Warehouse',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Maintenance Support',
                    'Keterangan Maintenance Support',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'TAP Apps Portal',
                    'Keterangan TAP Apps Portal',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Workshop Management '||'&'||' System Control',
                    'Keterangan Workshop Management '||'&'||' System Control',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Warehouse Management System',
                    'Keterangan Warehouse Management System',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Document Management System',
                    'Keterangan Document Management System',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Smart Agriculture (artificial intelligence '||'&'||' learning machine)',
                    'Keterangan Smart Agriculture (artificial intelligence '||'&'||' learning machine)',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Tracking Activity ',
                    'Keterangan Tracking Activity ',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Data Capture '||'&'||' System Control Infrastructure, Building',
                    'Keterangan Data Capture '||'&'||' System Control Infrastructure, Building',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Data Capture HPT (Web '||'&'||' Mobile)',
                    'Keterangan Data Capture HPT (Web '||'&'||' Mobile)',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Technology Alignment on Mechanization (Harvesting, Fertilizer, Upkeep) ',
                    'Keterangan Technology Alignment on Mechanization (Harvesting, Fertilizer, Upkeep) ',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Data Capture '||'&'||' System Control',
                    'Keterangan Data Capture '||'&'||' System Control',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'SAP LICENSES '||'&'||' SUPPORT',
                    'Keterangan SAP LICENSES '||'&'||' SUPPORT',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'RKB to RKH to Realisasi',
                    'Keterangan RKB to RKH to Realisasi',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'SWS Implementation',
                    'Keterangan SWS Implementation',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'SAP Implementation',
                    'Keterangan SAP Implementation',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'eBCC '||'&'||' ME Implementation',
                    'Keterangan eBCC '||'&'||' ME Implementation',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Peningkatan Standard Kompetensi IT Support',
                    'Keterangan Peningkatan Standard Kompetensi IT Support',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    'D00054',
                    'D00057',
                    'Peningkatan Kualitas IT Services Regional',
                    'Keterangan Peningkatan Kualitas IT Services Regional',
                    'NICHOLAS.BUDIHARDJA',
                    SYSDATE
                );
            COMMIT;
END : 2018-08-07 14:17:00
