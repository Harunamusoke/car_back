DATABASE MODEL AND SCRIPT 
----database_configurations 

### TEST DATATBASE

THE PARK CODE BACK AND FRONT API
--Register
BACKEND
 The sys expects the required details of the user as a POST data and processes them.
 The sys tries to create the user as well as registering it . if successful , a succesfull
message is returned.
 On error, an error message is returned

FRONTEND

 The code contains a register which talks with the backend and displays a error if
anything occurs .
NOTE:::
After successful register , the user has to be activated . So currently manually
activated the user.

--Login
BACKEND
 The system expects an email and password in the GET params.
 If the check and login is successful , it creates a web token for the user for next
requests.
 If an error occurs, a response of the error and the code are given out.
NOTE::::
The system responds with an array containing [ user => ......... , error =>........ ].

FRONTEND
o The frontend code contains a login form that only requires an email and a password
o For a successful login the login controller stores the token as the session of the user .
o For a failure , the error is displayed on the screen .

DASHBOARD AND ADMIN.
BACKEND ::
The backend contains a lot of apis to deal with the

 AUTH -- /auth/login and /auth/signup
 VEHICLES -- /api/vehicles
 USERS -- /api/users

Note :::: All those api require the X_PARK_USER header which is the web token sent by
the backend

SCRIPTS FOR TEST DATABASE
VEHICLES
INSERT INTO `car_res_park`.`vehicles`
(`name`,
`license_plate`,
`date_reg`,
`is_membered`)
VALUES('VOMX','HZFLUNM',NOW(),1),VALUES('WXJY','EKLTPZY',NOW(),0),VAL
UES('WTXC','WJXUVYZ',NOW(),0),VALUES('FUBG','LGMBHFR',NOW(),1),VALUES
('FOVX','JLGUSAW',NOW(),0),VALUES('ZUKE','DRCVLAK',NOW(),0),VALUES('TVJ
G','DNLJZAR',NOW(),0),VALUES('ENZK','NJSZDYI',NOW(),0),VALUES('RAXO','FIL
UTVH',NOW(),1),VALUES('DAMQ','KHAEUIR',NOW(),0);
-- SLOTS
INSERT INTO `car_res_park`.`slots`
(`address`,
`is_occupied`)
VALUES(0100,0)VALUES(1000,1)VALUES(0110,1)VALUES(0110,1)VALUES(1001,1
);

PARKING
INSERT INTO `car_res_park`.`parking`
(`vehicle_id`,
`token`,
`slot_id`,
`created_at`,
`created_by`,
)
VALUES(1,'SEQZMWBRKJ',1,now(),1),VALUES(1,'EXWOMVCHZJ',5,now(),1),VAL
UES(6,'WSZFLAIQGD',5,now(),6),VALUES(2,'THMIQEWRDC',5,now(),1),VALUES(3
,'FVXSJINZLQ',5,now(),1),VALUES(3,'ANDJZYOKQV',5,now(),1),VALUES(4,'ERLQV
PZWIB',2,now(),6),VALUES(2,'VWKZIXFEOL',3,now(),1),VALUES(2,'VERTSXWBQU'
,1,now(),6),VALUES(8,'RDAFEGSMUZ',1,now(),1),VALUES(1,'HQUYWSDARN',5,n
ow(),1),VALUES(6,'SMBGEZTQOJ',4,now(),1),VALUES(2,'NREZUFKHTB',5,now(),1)
,VALUES(2,'ZKGDEYUWNJ',3,now(),6),VALUES(8,'IZFYJORAWX',5,now(),6),VALUE
S(3,'QPEIAUBWFO',1,now(),6),VALUES(5,'IFAKBLXCYG',5,now(),1),VALUES(6,'XO
SFDAHZUQ',3,now(),6),VALUES(9,'SHADILPKXG',3,now(),6),VALUES(3,'IPMQDN
AVUJ',5,now(),6),VALUES(9,'KXGNFEDWTJ',3,now(),6),VALUES(5,'LYAIFZKNQS',5,
now(),6),VALUES(7,'UEWRLKHBQG',5,now(),1),VALUES(2,'XWROBKJLHD',5,now()
,1),VALUES(8,'CPIBDXATEY',2,now(),6),VALUES(3,'STCXDUJIMK',1,now(),1),VALU
ES(9,'TBUPXGLDJO',1,now(),1),VALUES(2,'VPIFTCRLZX',5,now(),6),VALUES(4,'TCI
DJWXQBZ',4,now(),6),VALUES(3,'OPQIXMBLGF',3,now(),1),VALUES(8,'YXEKDQL
OGS',2,now(),1),VALUES(1,'JCYDHQVFWO',2,now(),6),VALUES(3,'BFAIWJSYZG',5
,now(),1),VALUES(7,'JINVPUQMSE',3,now(),6),VALUES(8,'FHQZPNOBVC',2,now(),
1),VALUES(8,'SFGWKZPIRQ',4,now(),1),VALUES(3,'ORMTPCHYEK',4,now(),6),VAL
UES(4,'NWMZFYKUAL',4,now(),6),VALUES(7,'KNYPMOSFBL',5,now(),1),VALUES(
3,'KFYMSZTOCN',3,now(),6),VALUES(7,'VTROQFMYZE',5,now(),1),VALUES(4,'QO
KGVSCYXJ',5,now(),1),VALUES(6,'XLUJYHOCVQ',5,now(),1),VALUES(6,'EXGRMZN
YLK',3,now(),1),VALUES(3,'CDWKUAHFMS',1,now(),6),VALUES(6,'CAMKZTYOWR
',5,now(),1),VALUES(6,'CELQTKWIAP',5,now(),6),VALUES(5,'SNFEQYDZHK',5,now

(),6),VALUES(7,'WFGISZRAEQ',5,now(),1),VALUES(9,'KJCZULNAVG',3,now(),1),VA
LUES(1,'QAROPLEVCS',1,now(),6),VALUES(6,'ZHFIJKDSCW',5,now(),6),VALUES(8,
'STYGJHIEPM',2,now(),6),VALUES(1,'BNXYTQGFOZ',4,now(),1),VALUES(1,'ZYXISF
UHJK',1,now(),6),VALUES(8,'PGMNQSAHKD',5,now(),6),VALUES(1,'QWCJTADIY
M',5,now(),6),VALUES(9,'CMLUDBZYVJ',5,now(),1),VALUES(7,'DKFASRTWZY',4,n
ow(),6),VALUES(7,'VCQXMIWPZK',3,now(),1),VALUES(2,'QSXGOLUYTJ',3,now(),1
),VALUES(8,'EUGHALFBZI',2,now(),1),VALUES(6,'JWIBAMZTNX',5,now(),1),VALUE
S(4,'VTFROEIUYQ',3,now(),6),VALUES(3,'NJIGQMSHRZ',5,now(),6),VALUES(9,'SL
KAHOBZFI',3,now(),1),VALUES(6,'TRGAPWQFIH',4,now(),1),VALUES(8,'YKIPDAZ
OMB',1,now(),1),VALUES(8,'GPFVMARJHK',3,now(),6),VALUES(2,'BWVRFOHUDN
',3,now(),6),VALUES(3,'GUFSONPQRT',4,now(),1),VALUES(2,'IXQVNTRLKB',2,now
(),1),VALUES(6,'LJPMTUEOWA',4,now(),1),VALUES(9,'CASQPWYNUX',4,now(),6),
VALUES(9,'OTBEHLQFGI',5,now(),1),VALUES(9,'FJNGQOPVMS',4,now(),6),VALUE
S(7,'ZXQPWHFNBA',3,now(),1),VALUES(9,'MFSBRQVDOK',5,now(),1),VALUES(9,'
UIOESBLTKQ',4,now(),6),VALUES(6,'BYMSFLJXZR',2,now(),1),VALUES(3,'FBCDGL
VTZO',2,now(),1),VALUES(6,'UNQARCYVLK',3,now(),6),VALUES(3,'ECAHROPXJG',
3,now(),6),VALUES(6,'HKRCJYWMGP',2,now(),6),VALUES(1,'ZEGPXALOBJ',5,now(
),6),VALUES(6,'ZVSJEWAUPC',5,now(),6),VALUES(6,'BIPRFWYZXC',1,now(),1),VAL
UES(6,'WIRJUKDLYV',1,now(),1),VALUES(4,'PZAISMWOJR',5,now(),1),VALUES(9,'
EHAQMZTSCG',5,now(),1),VALUES(4,'JATGOMCKQB',3,now(),1),VALUES(6,'YQPJ
ZHMFTB',3,now(),1),VALUES(7,'MVKRTUBWOX',4,now(),6),VALUES(6,'PEOILMCT
YH',2,now(),6),VALUES(8,'XQAMUICKJR',5,now(),1),VALUES(4,'EFKZOCUNGA',5,
now(),1),VALUES(1,'HCRJAOTXDF',3,now(),6),VALUES(2,'XMRDNUSTCZ',4,now(),
1),VALUES(3,'NXBELIUZJO',4,now(),6),VALUES(8,'VKFUYWAZSB',3,now(),6);

TEST RATES SCRIPT
###
INSERT INTO `rates`( `name`, `rate`, `from`, `to`, `is_enabled`, `date_added`)
VALUES('Advanced', '1000', '3', '7', '0', now()),( 'Premium', '1500', '7', '10', '0', now()),('basic', '800', '1', '3', '1', now() );

##
