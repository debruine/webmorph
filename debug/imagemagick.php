<?php

error_reporting(E_ALL); 
ini_set( 'display_errors','1');

$svg = <<<EOT
<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE svg PUBLIC '-//W3C//DTD SVG 1.1//EN' 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd'>
<svg xmlns='http://www.w3.org/2000/svg'
     width='1350' height='1796'
     xmlns:xlink='http://www.w3.org/1999/xlink'>
<g id='lines' stroke='rgba(127,0, 127,.5)' stroke-width='10' stroke-linecap='round' fill='rgba(0,0,0,0)'>
    <!-- left pupil to left pupil -->
    <path id='line0' d='M 496.98 825.69
             L 496.98 825.69' />
    <!-- right pupil to right pupil -->
    <path id='line1' d='M 853.02 825.69
             L 853.02 825.69' />
    <!-- top of left iris to top of left iris -->
    <path id='line2' d='M 495.92 803.57
             C 486.87 803.43, 472.15 807.25, 465.64 812.31
             C 461.81 815.29, 460.7 825.18, 461.44 830.36
             C 462.27 836.25, 466.29 845.54, 470.86 849.21
             C 476.46 853.7, 487.97 857.49, 495.33 857.56
             C 502.66 857.63, 514.1 853.98, 519.81 849.65
             C 524.38 846.19, 528.58 837.31, 529.6 831.59
             C 530.52 826.41, 530 816.4, 526.28 813.31
             C 519.89 808, 505.06 803.72, 495.92 803.57' />
    <!-- top of right iris to top of right iris -->
    <path id='line3' d='M 854.09 803.57
             C 844.94 803.72, 830.11 808, 823.72 813.31
             C 820 816.4, 819.48 826.41, 820.4 831.59
             C 821.42 837.31, 825.62 846.19, 830.19 849.65
             C 835.9 853.98, 847.34 857.63, 854.67 857.56
             C 862.03 857.49, 873.54 853.7, 879.14 849.21
             C 883.71 845.54, 887.73 836.25, 888.57 830.36
             C 889.3 825.18, 888.19 815.29, 884.36 812.31
             C 877.85 807.25, 863.13 803.43, 854.09 803.57' />
    <!-- outside corner of left eye to inside corner of left eye -->
    <path id='line4' d='M 424.45 833.93
             Q 442.52 815.06, 452.09 810.72
             C 463.77 805.41, 482.43 801.22, 495.26 801.78
             C 508.82 802.37, 528.09 807.91, 540.05 814.54
             Q 554.59 822.59, 583.62 850.7' />
    <!-- inside corner of right eye to outside corner of right eye -->
    <path id='line5' d='M 766.38 850.7
             Q 795.41 822.59, 809.95 814.54
             C 821.91 807.91, 841.18 802.37, 854.74 801.78
             C 867.57 801.22, 886.23 805.41, 897.92 810.72
             Q 907.48 815.06, 925.55 833.93' />
    <!-- outside corner of left eye to inside corner of left eye -->
    <path id='line6' d='M 424.45 833.93
             Q 442.16 850.06, 451.19 853.67
             C 462.82 858.33, 480.64 861.39, 493.31 861.52
             C 506.61 861.66, 524.35 856.17, 537.74 854.57
             Q 551.45 852.93, 583.62 850.7' />
    <!-- inside corner of right eye to outside corner of right eye -->
    <path id='line7' d='M 766.38 850.7
             Q 798.55 852.93, 812.26 854.57
             C 825.65 856.17, 843.39 861.66, 856.69 861.52
             C 869.36 861.39, 887.18 858.33, 898.82 853.67
             Q 907.84 850.06, 925.55 833.93' />
    <!-- left of crease above left eye to right of crease above left eye -->
    <path id='line8' d='M 404.4 818.17
             Q 427.83 796.93, 439.68 791.89
             C 453.74 785.91, 475.37 781.95, 490.77 781.43
             C 505.06 780.94, 525.86 782.92, 538.67 788.54
             Q 550.73 793.83, 573.66 817.78' />
    <!-- left of crease above right eye to right of crease above right eye -->
    <path id='line9' d='M 776.35 817.78
             Q 799.27 793.83, 811.33 788.54
             C 824.14 782.92, 844.94 780.94, 859.23 781.43
             C 874.63 781.95, 896.26 785.91, 910.32 791.89
             Q 922.17 796.93, 945.6 818.17' />
    <!-- bottom-left of circle under left eye to top-right of circle under left eye -->
    <path id='line10' d='M 481.44 949.92
             Q 525.73 935.8, 542.34 925.84
             Q 558.01 916.43, 589.07 885.37' />
    <!-- top-left of circle under right eye to bottom-right of circle under right eye -->
    <path id='line11' d='M 760.94 885.37
             Q 791.99 916.43, 807.67 925.84
             Q 824.27 935.8, 868.56 949.92' />
    <!-- left side of bridge of nose (towards eyebrow) to bottom-centre of nose -->
    <path id='line12' d='M 638.56 793.86
             Q 650.36 826.14, 652.58 840.44
             C 655.13 856.87, 654.77 879.54, 654.43 896.27
             C 654.08 913.77, 652.69 937.21, 650.27 954.54
             C 647.33 975.61, 633.15 1004.45, 636.58 1024.27
             Q 640.56 1047.33, 675 1097.48' />
    <!-- right side of bridge of nose (towards eyebrow) to bottom-centre of nose -->
    <path id='line13' d='M 711.44 793.86
             Q 699.64 826.14, 697.42 840.44
             C 694.87 856.87, 695.23 879.54, 695.57 896.27
             C 695.92 913.77, 697.31 937.21, 699.73 954.54
             C 702.67 975.61, 716.85 1004.45, 713.43 1024.27
             Q 709.44 1047.33, 675 1097.48' />
    <!-- top of left outer nostril to bottom-centre of nose -->
    <path id='line14' d='M 585.76 1017.89
             Q 575.63 1034.78, 574.48 1042.52
             C 573.24 1050.77, 573.93 1064.15, 577.79 1071.19
             C 581.69 1078.3, 595.44 1089.99, 600.33 1089.69
             C 603.84 1089.48, 601.3 1071.37, 605.78 1069.48
             C 613 1066.44, 630.66 1069.41, 639.34 1073.25
             C 645.77 1076.1, 650.17 1087.72, 656.16 1091.79
             Q 660.87 1094.99, 675 1097.48' />
    <!-- top of right outer nostril to bottom-centre of nose -->
    <path id='line15' d='M 764.24 1017.89
             Q 774.37 1034.78, 775.52 1042.52
             C 776.76 1050.77, 776.07 1064.15, 772.21 1071.19
             C 768.31 1078.3, 754.56 1089.99, 749.67 1089.69
             C 746.16 1089.48, 748.7 1071.37, 744.23 1069.48
             C 737 1066.44, 719.34 1069.41, 710.66 1073.25
             C 704.23 1076.1, 699.83 1087.72, 693.84 1091.79
             Q 689.13 1094.99, 675 1097.48' />
    <!-- left corner of left eyebrow to bottom-right of left eyebrow -->
    <path id='line16' d='M 339.87 765.86
             Q 356.8 729.19, 368.76 718.66
             C 382.04 706.99, 406.45 694.51, 424.01 691.86
             C 446.01 688.54, 478.44 693.31, 500.65 698.75
             C 526.67 705.13, 565.47 716.68, 584.76 731.26
             Q 592.43 737.06, 590.53 766.68' />
    <!-- bottom-left or right eyebrow to right corner of right eyebrow -->
    <path id='line17' d='M 759.47 766.68
             Q 757.57 737.06, 765.25 731.26
             C 784.53 716.68, 823.33 705.13, 849.35 698.75
             C 871.56 693.31, 903.99 688.54, 925.99 691.86
             C 943.55 694.51, 967.96 706.99, 981.24 718.66
             Q 993.2 729.19, 1010.13 765.86' />
    <!-- left corner of left eyebrow to bottom-right of left eyebrow -->
    <path id='line18' d='M 339.87 765.86
             Q 387.73 735.36, 409.95 731.54
             C 434.21 727.38, 469.83 734.41, 494.81 739.27
             Q 524 744.95, 590.53 766.68' />
    <!-- bottom-left or right eyebrow to right corner of right eyebrow -->
    <path id='line19' d='M 759.47 766.68
             Q 826 744.95, 855.19 739.27
             C 880.17 734.41, 915.79 727.38, 940.05 731.54
             Q 962.27 735.36, 1010.13 765.86' />
    <!-- left corner of the mouth to right corner of the mouth -->
    <path id='line20' d='M 539.96 1220.69
             Q 572.97 1204.6, 587.61 1199.01
             C 602.12 1193.47, 622.13 1184.78, 637.13 1183.58
             C 648.35 1182.68, 663.64 1192.02, 675 1192.02
             C 686.36 1192.02, 701.65 1182.68, 712.88 1183.58
             C 727.87 1184.78, 747.88 1193.47, 762.39 1199.01
             Q 777.03 1204.6, 810.04 1220.69' />
    <!-- left corner of the mouth to right corner of the mouth -->
    <path id='line21' d='M 539.96 1220.69
             Q 574.98 1220.61, 589.99 1220.42
             C 602.17 1220.27, 618.43 1219.21, 630.59 1219.56
             C 643.93 1219.95, 661.68 1222.89, 675 1222.89
             C 688.32 1222.89, 706.07 1219.95, 719.41 1219.56
             C 731.57 1219.21, 747.83 1220.27, 760.01 1220.42
             Q 775.02 1220.61, 810.04 1220.69' />
    <!-- left corner of the mouth to right corner of the mouth -->
    <path id='line22' d='M 539.96 1220.69
             Q 575 1220.63, 590.01 1220.36
             C 602.2 1220.14, 618.46 1218.72, 630.63 1219.06
             C 643.96 1219.43, 661.69 1222.74, 675 1222.74
             C 688.31 1222.74, 706.04 1219.43, 719.38 1219.06
             C 731.54 1218.72, 747.8 1220.14, 759.99 1220.36
             Q 775 1220.63, 810.04 1220.69' />
    <!-- left corner of the mouth to right corner of the mouth -->
    <path id='line23' d='M 539.96 1220.69
             Q 566.38 1248.07, 579.51 1257.11
             C 590.69 1264.81, 607.91 1272.81, 620.98 1276.48
             C 636.55 1280.85, 658.79 1283.89, 675 1283.89
             C 691.21 1283.89, 713.45 1280.85, 729.02 1276.48
             C 742.09 1272.81, 759.31 1264.81, 770.49 1257.11
             Q 783.62 1248.07, 810.04 1220.69' />
    <!-- where top of left ear meets head to where bottom of left ear meets head -->
    <path id='line24' d='M 300.68 817.87
             Q 296.61 860.07, 296.34 878.18
             C 296.07 897.35, 296.83 923.09, 298.89 942.14
             C 300.98 961.45, 306.54 986.93, 310.16 1006.06
             Q 314.03 1026.45, 323.83 1073.88' />
    <!-- where top of right ear meets head to where bottom of right ear meets head -->
    <path id='line25' d='M 1049.32 817.87
             Q 1053.4 860.07, 1053.66 878.18
             C 1053.94 897.35, 1053.17 923.09, 1051.11 942.14
             C 1049.02 961.45, 1043.46 986.93, 1039.84 1006.06
             Q 1035.98 1026.45, 1026.17 1073.88' />
    <!-- where top of left ear meets head to where bottom of left ear meets head -->
    <path id='line26' d='M 300.68 817.87
             Q 261.41 782.02, 251.2 784.6
             C 242.44 786.81, 237.44 818.83, 237.42 833.84
             C 237.4 859.85, 246.31 895.22, 251.06 921.36
             C 255.57 946.13, 260.2 979.93, 268.3 1003.53
             C 274.87 1022.68, 287.89 1048.57, 299.97 1063.86
             Q 304.55 1069.67, 323.83 1073.88' />
    <!-- where top of right ear meets head to where bottom of right ear meets head -->
    <path id='line27' d='M 1049.32 817.87
             Q 1088.58 782.02, 1098.8 784.6
             C 1107.56 786.81, 1112.56 818.83, 1112.58 833.84
             C 1112.61 859.85, 1103.7 895.22, 1098.94 921.36
             C 1094.43 946.13, 1089.8 979.93, 1081.7 1003.53
             C 1075.13 1022.68, 1062.1 1048.56, 1050.03 1063.86
             Q 1045.44 1069.67, 1026.17 1073.88' />
    <!-- where top of left ear meets head to where top of right ear meets head -->
    <path id='line28' d='M 300.68 817.87
             Q 303.19 740.05, 308.3 707.33
             C 313.48 674.24, 325.29 630.63, 334.98 598.49
             C 344.29 567.59, 354.51 523.47, 371.64 497.19
             C 387 473.62, 418.66 446.5, 443.28 432.3
             C 471.27 416.17, 514.9 401.18, 547.01 396.09
             C 584.42 390.17, 636.6 395.61, 675 395.61
             C 713.4 395.61, 765.58 390.17, 802.99 396.09
             C 835.1 401.18, 878.73 416.17, 906.72 432.3
             C 931.34 446.5, 963 473.62, 978.37 497.19
             C 995.49 523.47, 1005.71 567.59, 1015.02 598.49
             C 1024.71 630.63, 1036.53 674.24, 1041.7 707.33
             Q 1046.82 740.05, 1049.32 817.87' />
    <!-- left side of the neck, about 3cm down from the jaw to right side of the neck, about 3cm down from the jaw -->
    <path id='line29' d='M 407.95 1456.1
             Q 268.44 1266.5, 238.87 1174.89
             C 203.99 1066.82, 193.93 905.17, 193.13 790.52
             C 192.73 734.23, 216.39 658.68, 234.87 605.08
             C 254.37 548.57, 286.13 472.32, 319.74 423.49
             C 352.14 376.41, 406.02 312.6, 454.91 285.38
             C 512.6 253.25, 608.97 225.65, 675 225.65
             C 741.03 225.65, 837.4 253.25, 895.09 285.38
             C 943.98 312.6, 997.86 376.41, 1030.26 423.49
             C 1063.87 472.32, 1095.63 548.57, 1115.13 605.08
             C 1133.62 658.68, 1157.27 734.23, 1156.87 790.52
             C 1156.07 905.17, 1146.01 1066.82, 1111.13 1174.89
             Q 1081.56 1266.5, 942.05 1456.1' />
    <!-- top of left smile line to bottom of left smile line -->
    <path id='line30' d='M 547.09 1088.5
             Q 520.33 1118.23, 511.5 1132.61
             Q 502.06 1147.98, 486.16 1187.64' />
    <!-- top of right smile line to bottom of right smile line -->
    <path id='line31' d='M 802.92 1088.5
             Q 829.67 1118.23, 838.5 1132.61
             Q 847.94 1147.98, 863.84 1187.64' />
    <!-- top-left of left cheekbone to bottom-right of left cheekbone -->
    <path id='line32' d='M 343.92 927.84
             Q 377.01 973.73, 395.31 988.04
             Q 412.16 1001.22, 461.08 1019.47' />
    <!-- top-right of right cheekbone to bottom-left of right cheekbone -->
    <path id='line33' d='M 1006.08 927.84
             Q 972.99 973.73, 954.7 988.04
             Q 937.84 1001.22, 888.93 1019.47' />
    <!-- top-left of philtrum to bottom-left of philtrum -->
    <path id='line34' d='M 648.22 1124.24
             L 642.16 1159.74' />
    <!-- top-right of philtrum to bottom-right of philtrum -->
    <path id='line35' d='M 701.78 1124.24
             L 707.84 1159.74' />
    <!-- top of chin dimple to bottom of chin dimple -->
    <path id='line36' d='M 675 1365.49
             L 675 1413.88' />
    <!-- left of horizontal crease under chin to right of horizontal crease under chin -->
    <path id='line37' d='M 615.6 1327.72
             Q 657.18 1315.79, 675 1315.79
             Q 692.82 1315.79, 734.4 1327.72' />
    <!-- where the left side of the neck meets the face to left side of the neck, about 3cm down from the jaw -->
    <path id='line38' d='M 405.86 1304.84
             L 407.95 1456.1' />
    <!-- where the right side of the neck meets the face to right side of the neck, about 3cm down from the jaw -->
    <path id='line39' d='M 944.14 1304.84
             L 942.05 1456.1' />
    <!-- left side of the neck, about 3cm down from the jaw to right side of the neck, about 3cm down from the jaw -->
    <path id='line40' d='M 407.95 1456.1
             Q 515.52 1553.47, 569.6 1570.01
             C 627.25 1587.64, 722.75 1587.64, 780.4 1570.01
             Q 834.48 1553.47, 942.05 1456.1' />
    <!-- where bottom of left ear meets head to where the left side of the neck meets the face -->
    <path id='line41' d='M 323.83 1073.88
             Q 337.04 1165.56, 350.04 1202.17
             Q 361.65 1234.85, 405.86 1304.84' />
    <!-- where the left side of the neck meets the face to where the right side of the neck meets the face -->
    <path id='line42' d='M 405.86 1304.84
             Q 453.21 1356.87, 476.27 1375.83
             C 500.64 1395.88, 535.19 1423.04, 563.96 1434.87
             C 594.8 1447.55, 641.69 1457.55, 675 1457.55
             C 708.31 1457.55, 755.2 1447.55, 786.04 1434.87
             C 814.81 1423.04, 849.36 1395.88, 873.73 1375.83
             Q 896.79 1356.87, 944.14 1304.84' />
    <!-- where the right side of the neck meets the face to where bottom of right ear meets head -->
    <path id='line43' d='M 944.14 1304.84
             Q 988.35 1234.85, 999.96 1202.17
             Q 1012.96 1165.56, 1026.17 1073.88' />
</g>
<defs>
    <symbol id='pt'>
        <path  d='
               M 11 16
               L 16.88 19.09
               L 15.76 12.55
               L 20.51 7.91
               L 13.94 6.95
               L 11 1
               L 8.06 6.95
               L 1.49 7.91
               L 6.24 12.55
               L 5.12 19.09
               L 11 16
         '/>
    </symbol>
</defs>
<g id='points' stroke='rgba(0,255,0,255)' fill='rgba(0,255,0,255)'
   stroke-width='2' stroke-linecap='round'
   transform='translate(-11, -11)'>
    <!-- left pupil -->
        <use xlink:href='#pt' id='pt0' x='496.98' y='825.69'/>
    <!-- right pupil -->
        <use xlink:href='#pt' id='pt1' x='853.02' y='825.69'/>
    <!-- top of left iris -->
        <use xlink:href='#pt' id='pt2' x='495.92' y='803.57'/>
    <!-- top-left of left iris -->
        <use xlink:href='#pt' id='pt3' x='465.64' y='812.31'/>
    <!-- left of left iris -->
        <use xlink:href='#pt' id='pt4' x='461.44' y='830.36'/>
    <!-- bottom-left of left iris -->
        <use xlink:href='#pt' id='pt5' x='470.86' y='849.21'/>
    <!-- bottom of left iris -->
        <use xlink:href='#pt' id='pt6' x='495.33' y='857.56'/>
    <!-- bottom-right of left iris -->
        <use xlink:href='#pt' id='pt7' x='519.81' y='849.65'/>
    <!-- right of left iris -->
        <use xlink:href='#pt' id='pt8' x='529.6' y='831.59'/>
    <!-- top-right of left iris -->
        <use xlink:href='#pt' id='pt9' x='526.28' y='813.31'/>
    <!-- top of right iris -->
        <use xlink:href='#pt' id='pt10' x='854.09' y='803.57'/>
    <!-- top-left of right iris -->
        <use xlink:href='#pt' id='pt11' x='823.72' y='813.31'/>
    <!-- left of right iris -->
        <use xlink:href='#pt' id='pt12' x='820.4' y='831.59'/>
    <!-- bottom-left of right iris -->
        <use xlink:href='#pt' id='pt13' x='830.19' y='849.65'/>
    <!-- bottom of right iris -->
        <use xlink:href='#pt' id='pt14' x='854.67' y='857.56'/>
    <!-- bottom-right of right iris -->
        <use xlink:href='#pt' id='pt15' x='879.14' y='849.21'/>
    <!-- right of right iris -->
        <use xlink:href='#pt' id='pt16' x='888.57' y='830.36'/>
    <!-- top-right of right iris -->
        <use xlink:href='#pt' id='pt17' x='884.36' y='812.31'/>
    <!-- outside corner of left eye -->
        <use xlink:href='#pt' id='pt18' x='424.45' y='833.93'/>
    <!-- top-left of left eye -->
        <use xlink:href='#pt' id='pt19' x='452.09' y='810.72'/>
    <!-- top-centre of left eye -->
        <use xlink:href='#pt' id='pt20' x='495.26' y='801.78'/>
    <!-- top-right of left eye -->
        <use xlink:href='#pt' id='pt21' x='540.05' y='814.54'/>
    <!-- inside corner of left eye -->
        <use xlink:href='#pt' id='pt22' x='583.62' y='850.7'/>
    <!-- inside corner of right eye -->
        <use xlink:href='#pt' id='pt23' x='766.38' y='850.7'/>
    <!-- top-left of right eye -->
        <use xlink:href='#pt' id='pt24' x='809.95' y='814.54'/>
    <!-- top-centre of right eye -->
        <use xlink:href='#pt' id='pt25' x='854.74' y='801.78'/>
    <!-- top-right of right eye -->
        <use xlink:href='#pt' id='pt26' x='897.92' y='810.72'/>
    <!-- outside corner of right eye -->
        <use xlink:href='#pt' id='pt27' x='925.55' y='833.93'/>
    <!-- bottom-left of left eye -->
        <use xlink:href='#pt' id='pt28' x='451.19' y='853.67'/>
    <!-- bottom-centre of left eye -->
        <use xlink:href='#pt' id='pt29' x='493.31' y='861.52'/>
    <!-- bottom-right of left eye -->
        <use xlink:href='#pt' id='pt30' x='537.74' y='854.57'/>
    <!-- bottom-left of right eye -->
        <use xlink:href='#pt' id='pt31' x='812.26' y='854.57'/>
    <!-- bottom-centre of right eye -->
        <use xlink:href='#pt' id='pt32' x='856.69' y='861.52'/>
    <!-- bottom-right of right eye -->
        <use xlink:href='#pt' id='pt33' x='898.82' y='853.67'/>
    <!-- left of crease above left eye -->
        <use xlink:href='#pt' id='pt34' x='404.4' y='818.17'/>
    <!-- centre-left of crease above left eye -->
        <use xlink:href='#pt' id='pt35' x='439.68' y='791.89'/>
    <!-- centre of crease above left eye (above pupil) -->
        <use xlink:href='#pt' id='pt36' x='490.77' y='781.43'/>
    <!-- centre-right of crease above left eye -->
        <use xlink:href='#pt' id='pt37' x='538.67' y='788.54'/>
    <!-- right of crease above left eye -->
        <use xlink:href='#pt' id='pt38' x='573.66' y='817.78'/>
    <!-- left of crease above right eye -->
        <use xlink:href='#pt' id='pt39' x='776.35' y='817.78'/>
    <!-- centre-left of crease above right eye -->
        <use xlink:href='#pt' id='pt40' x='811.33' y='788.54'/>
    <!-- centre of crease above right eye (above pupil) -->
        <use xlink:href='#pt' id='pt41' x='859.23' y='781.43'/>
    <!-- centre-right of crease above right eye -->
        <use xlink:href='#pt' id='pt42' x='910.32' y='791.89'/>
    <!-- right of crease above right eye -->
        <use xlink:href='#pt' id='pt43' x='945.6' y='818.17'/>
    <!-- bottom-left of circle under left eye -->
        <use xlink:href='#pt' id='pt44' x='481.44' y='949.92'/>
    <!-- centre of circle under left eye -->
        <use xlink:href='#pt' id='pt45' x='542.34' y='925.84'/>
    <!-- top-right of circle under left eye -->
        <use xlink:href='#pt' id='pt46' x='589.07' y='885.37'/>
    <!-- top-left of circle under right eye -->
        <use xlink:href='#pt' id='pt47' x='760.94' y='885.37'/>
    <!-- centre of circle under right eye -->
        <use xlink:href='#pt' id='pt48' x='807.67' y='925.84'/>
    <!-- bottom-right of circle under right eye -->
        <use xlink:href='#pt' id='pt49' x='868.56' y='949.92'/>
    <!-- left side of bridge of nose (towards eyebrow) -->
        <use xlink:href='#pt' id='pt50' x='638.56' y='793.86'/>
    <!-- left side of bridge of nose (in line with inside corners of eyes) -->
        <use xlink:href='#pt' id='pt51' x='652.58' y='840.44'/>
    <!-- left side of bridge of nose -->
        <use xlink:href='#pt' id='pt52' x='654.43' y='896.27'/>
    <!-- left side of bridge of nose -->
        <use xlink:href='#pt' id='pt53' x='650.27' y='954.54'/>
    <!-- left side of tip of nose (most bulbous part) -->
        <use xlink:href='#pt' id='pt54' x='636.58' y='1024.27'/>
    <!-- bottom-centre of nose -->
        <use xlink:href='#pt' id='pt55' x='675' y='1097.48'/>
    <!-- right side of bridge of nose (towards eyebrow) -->
        <use xlink:href='#pt' id='pt56' x='711.44' y='793.86'/>
    <!-- right side of bridge of nose (in line with inside corners of eyes) -->
        <use xlink:href='#pt' id='pt57' x='697.42' y='840.44'/>
    <!-- right side of bridge of nose -->
        <use xlink:href='#pt' id='pt58' x='695.57' y='896.27'/>
    <!-- right side of bridge of nose -->
        <use xlink:href='#pt' id='pt59' x='699.73' y='954.54'/>
    <!-- right side of tip of nose (most bulbous part) -->
        <use xlink:href='#pt' id='pt60' x='713.43' y='1024.27'/>
    <!-- top of left outer nostril -->
        <use xlink:href='#pt' id='pt61' x='585.76' y='1017.89'/>
    <!-- upper-left of left outer nostril -->
        <use xlink:href='#pt' id='pt62' x='574.48' y='1042.52'/>
    <!-- lower-left of left outer nostril -->
        <use xlink:href='#pt' id='pt63' x='577.79' y='1071.19'/>
    <!-- bottom of left out nostril -->
        <use xlink:href='#pt' id='pt64' x='600.33' y='1089.69'/>
    <!-- left edge of left inner nostril -->
        <use xlink:href='#pt' id='pt65' x='605.78' y='1069.48'/>
    <!-- top of right outer nostril -->
        <use xlink:href='#pt' id='pt66' x='764.24' y='1017.89'/>
    <!-- upper-right of right outer nostril -->
        <use xlink:href='#pt' id='pt67' x='775.52' y='1042.52'/>
    <!-- lower-right of right outer nostril -->
        <use xlink:href='#pt' id='pt68' x='772.21' y='1071.19'/>
    <!-- bottom of right outer nostril -->
        <use xlink:href='#pt' id='pt69' x='749.67' y='1089.69'/>
    <!-- right edge of right inner nostril -->
        <use xlink:href='#pt' id='pt70' x='744.23' y='1069.48'/>
    <!-- left corner of left eyebrow -->
        <use xlink:href='#pt' id='pt71' x='339.87' y='765.86'/>
    <!-- top of left eyebrow -->
        <use xlink:href='#pt' id='pt72' x='368.76' y='718.66'/>
    <!-- top of left eyebrow -->
        <use xlink:href='#pt' id='pt73' x='424.01' y='691.86'/>
    <!-- top of left eyebrow above pupil -->
        <use xlink:href='#pt' id='pt74' x='500.65' y='698.75'/>
    <!-- top-right of left eyebrow -->
        <use xlink:href='#pt' id='pt75' x='584.76' y='731.26'/>
    <!-- bottom-right of left eyebrow -->
        <use xlink:href='#pt' id='pt76' x='590.53' y='766.68'/>
    <!-- bottom-left or right eyebrow -->
        <use xlink:href='#pt' id='pt77' x='759.47' y='766.68'/>
    <!-- top-left of right eyebrow -->
        <use xlink:href='#pt' id='pt78' x='765.25' y='731.26'/>
    <!-- top of right eyebrow above pupil -->
        <use xlink:href='#pt' id='pt79' x='849.35' y='698.75'/>
    <!-- top of right eyebrow -->
        <use xlink:href='#pt' id='pt80' x='925.99' y='691.86'/>
    <!-- top of right eyebrow -->
        <use xlink:href='#pt' id='pt81' x='981.24' y='718.66'/>
    <!-- right corner of right eyebrow -->
        <use xlink:href='#pt' id='pt82' x='1010.13' y='765.86'/>
    <!-- bottom of left eyebrow -->
        <use xlink:href='#pt' id='pt83' x='409.95' y='731.54'/>
    <!-- bottom of left eyebrow above pupil -->
        <use xlink:href='#pt' id='pt84' x='494.81' y='739.27'/>
    <!-- bottom of right eyebrow above pupil -->
        <use xlink:href='#pt' id='pt85' x='855.19' y='739.27'/>
    <!-- bottom of right eyebrow -->
        <use xlink:href='#pt' id='pt86' x='940.05' y='731.54'/>
    <!-- left corner of the mouth -->
        <use xlink:href='#pt' id='pt87' x='539.96' y='1220.69'/>
    <!-- left of top of upper lip -->
        <use xlink:href='#pt' id='pt88' x='587.61' y='1199.01'/>
    <!-- left point of upper lip -->
        <use xlink:href='#pt' id='pt89' x='637.13' y='1183.58'/>
    <!-- centre of top of upper lip -->
        <use xlink:href='#pt' id='pt90' x='675' y='1192.02'/>
    <!-- right point of upper lip -->
        <use xlink:href='#pt' id='pt91' x='712.88' y='1183.58'/>
    <!-- right of top of upper lip -->
        <use xlink:href='#pt' id='pt92' x='762.39' y='1199.01'/>
    <!-- right corner of the mouth -->
        <use xlink:href='#pt' id='pt93' x='810.04' y='1220.69'/>
    <!-- left of bottom of upper lip -->
        <use xlink:href='#pt' id='pt94' x='589.99' y='1220.42'/>
    <!-- left-centre of bottom of upper lip -->
        <use xlink:href='#pt' id='pt95' x='630.59' y='1219.56'/>
    <!-- centre of bottom of upper lip -->
        <use xlink:href='#pt' id='pt96' x='675' y='1222.89'/>
    <!-- right-centre of bottom of upper lip -->
        <use xlink:href='#pt' id='pt97' x='719.41' y='1219.56'/>
    <!-- right of bottom of upper lip -->
        <use xlink:href='#pt' id='pt98' x='760.01' y='1220.42'/>
    <!-- left of top of lower lip -->
        <use xlink:href='#pt' id='pt99' x='590.01' y='1220.36'/>
    <!-- left-centre of top of lower lip -->
        <use xlink:href='#pt' id='pt100' x='630.63' y='1219.06'/>
    <!-- centre of top of lower lip -->
        <use xlink:href='#pt' id='pt101' x='675' y='1222.74'/>
    <!-- right-centre of top of lower lip -->
        <use xlink:href='#pt' id='pt102' x='719.38' y='1219.06'/>
    <!-- right of top of lower lip -->
        <use xlink:href='#pt' id='pt103' x='759.99' y='1220.36'/>
    <!-- left of bottom of lower lip -->
        <use xlink:href='#pt' id='pt104' x='579.51' y='1257.11'/>
    <!-- left-center of bottom of lower lip -->
        <use xlink:href='#pt' id='pt105' x='620.98' y='1276.48'/>
    <!-- centre of bottom of lower lip -->
        <use xlink:href='#pt' id='pt106' x='675' y='1283.89'/>
    <!-- right-centre of bottom of lower lip -->
        <use xlink:href='#pt' id='pt107' x='729.02' y='1276.48'/>
    <!-- right of bottom of lower lip -->
        <use xlink:href='#pt' id='pt108' x='770.49' y='1257.11'/>
    <!-- where top of left ear meets head -->
        <use xlink:href='#pt' id='pt109' x='300.68' y='817.87'/>
    <!-- middle of where left ear meets head -->
        <use xlink:href='#pt' id='pt110' x='298.89' y='942.14'/>
    <!-- where bottom of left ear meets head -->
        <use xlink:href='#pt' id='pt111' x='323.83' y='1073.88'/>
    <!-- where top of right ear meets head -->
        <use xlink:href='#pt' id='pt112' x='1049.32' y='817.87'/>
    <!-- middle of where right ear meets head -->
        <use xlink:href='#pt' id='pt113' x='1051.11' y='942.14'/>
    <!-- where bottom of right ear meets head -->
        <use xlink:href='#pt' id='pt114' x='1026.17' y='1073.88'/>
    <!-- top of left ear -->
        <use xlink:href='#pt' id='pt115' x='251.2' y='784.6'/>
    <!-- upper-middle outside of left ear -->
        <use xlink:href='#pt' id='pt116' x='237.42' y='833.84'/>
    <!-- middle outside of left ear -->
        <use xlink:href='#pt' id='pt117' x='251.06' y='921.36'/>
    <!-- lower-middle outside of left ear -->
        <use xlink:href='#pt' id='pt118' x='268.3' y='1003.53'/>
    <!-- bottom of left earlobe -->
        <use xlink:href='#pt' id='pt119' x='299.97' y='1063.86'/>
    <!-- top of right ear -->
        <use xlink:href='#pt' id='pt120' x='1098.8' y='784.6'/>
    <!-- upper-middle outside of right ear -->
        <use xlink:href='#pt' id='pt121' x='1112.58' y='833.84'/>
    <!-- middle outside of right ear -->
        <use xlink:href='#pt' id='pt122' x='1098.94' y='921.36'/>
    <!-- lower-middle outside of right ear -->
        <use xlink:href='#pt' id='pt123' x='1081.7' y='1003.53'/>
    <!-- bottom of right earlobe -->
        <use xlink:href='#pt' id='pt124' x='1050.03' y='1063.86'/>
    <!-- left jaw between ear and neck -->
        <use xlink:href='#pt' id='pt125' x='350.04' y='1202.17'/>
    <!-- where the left side of the neck meets the face -->
        <use xlink:href='#pt' id='pt126' x='405.86' y='1304.84'/>
    <!-- left jaw -->
        <use xlink:href='#pt' id='pt127' x='476.27' y='1375.83'/>
    <!-- bottom left corner of the chin -->
        <use xlink:href='#pt' id='pt128' x='563.96' y='1434.87'/>
    <!-- bottom centre of the chin -->
        <use xlink:href='#pt' id='pt129' x='675' y='1457.55'/>
    <!-- bottom right corner of the chin -->
        <use xlink:href='#pt' id='pt130' x='786.04' y='1434.87'/>
    <!-- right jaw -->
        <use xlink:href='#pt' id='pt131' x='873.73' y='1375.83'/>
    <!-- where the right side of the neck meets the face -->
        <use xlink:href='#pt' id='pt132' x='944.14' y='1304.84'/>
    <!-- right jaw between ear and neck -->
        <use xlink:href='#pt' id='pt133' x='999.96' y='1202.17'/>
    <!-- hairline -->
        <use xlink:href='#pt' id='pt134' x='308.3' y='707.33'/>
    <!-- hairline -->
        <use xlink:href='#pt' id='pt135' x='334.98' y='598.49'/>
    <!-- left temple hairline -->
        <use xlink:href='#pt' id='pt136' x='371.64' y='497.19'/>
    <!-- hairline -->
        <use xlink:href='#pt' id='pt137' x='443.28' y='432.3'/>
    <!-- hairline -->
        <use xlink:href='#pt' id='pt138' x='547.01' y='396.09'/>
    <!-- centre of hairline -->
        <use xlink:href='#pt' id='pt139' x='675' y='395.61'/>
    <!-- hairline -->
        <use xlink:href='#pt' id='pt140' x='802.99' y='396.09'/>
    <!-- hairline -->
        <use xlink:href='#pt' id='pt141' x='906.72' y='432.3'/>
    <!-- right temple hairline -->
        <use xlink:href='#pt' id='pt142' x='978.37' y='497.19'/>
    <!-- hairline -->
        <use xlink:href='#pt' id='pt143' x='1015.02' y='598.49'/>
    <!-- hairline -->
        <use xlink:href='#pt' id='pt144' x='1041.7' y='707.33'/>
    <!-- left side of the neck, about 3cm down from the jaw -->
        <use xlink:href='#pt' id='pt145' x='407.95' y='1456.1'/>
    <!-- outside left earlobe (creates oval around head) -->
        <use xlink:href='#pt' id='pt146' x='238.87' y='1174.89'/>
    <!-- about 2cm to the left of the top of the left ear (creates oval around head) -->
        <use xlink:href='#pt' id='pt147' x='193.13' y='790.52'/>
    <!-- outside left temple (creates oval around head) -->
        <use xlink:href='#pt' id='pt148' x='234.87' y='605.08'/>
    <!-- top of head above left temple -->
        <use xlink:href='#pt' id='pt149' x='319.74' y='423.49'/>
    <!-- top of head between left temple and centre -->
        <use xlink:href='#pt' id='pt150' x='454.91' y='285.38'/>
    <!-- centre top of head -->
        <use xlink:href='#pt' id='pt151' x='675' y='225.65'/>
    <!-- top of head between right temple and centre -->
        <use xlink:href='#pt' id='pt152' x='895.09' y='285.38'/>
    <!-- top of head above right temple -->
        <use xlink:href='#pt' id='pt153' x='1030.26' y='423.49'/>
    <!-- outside right temple (creates oval around head) -->
        <use xlink:href='#pt' id='pt154' x='1115.13' y='605.08'/>
    <!-- about 2cm to the right of the top of the right ear (creates oval around head) -->
        <use xlink:href='#pt' id='pt155' x='1156.87' y='790.52'/>
    <!-- outside right earlobe (creates oval around head) -->
        <use xlink:href='#pt' id='pt156' x='1111.13' y='1174.89'/>
    <!-- right side of the neck, about 3cm down from the jaw -->
        <use xlink:href='#pt' id='pt157' x='942.05' y='1456.1'/>
    <!-- top of left smile line -->
        <use xlink:href='#pt' id='pt158' x='547.09' y='1088.5'/>
    <!-- centre of left smile line -->
        <use xlink:href='#pt' id='pt159' x='511.5' y='1132.61'/>
    <!-- bottom of left smile line -->
        <use xlink:href='#pt' id='pt160' x='486.16' y='1187.64'/>
    <!-- top of right smile line -->
        <use xlink:href='#pt' id='pt161' x='802.92' y='1088.5'/>
    <!-- centre of right smile line -->
        <use xlink:href='#pt' id='pt162' x='838.5' y='1132.61'/>
    <!-- bottom of right smile line -->
        <use xlink:href='#pt' id='pt163' x='863.84' y='1187.64'/>
    <!-- top-left of left cheekbone -->
        <use xlink:href='#pt' id='pt164' x='343.92' y='927.84'/>
    <!-- centre of left cheekbone -->
        <use xlink:href='#pt' id='pt165' x='395.31' y='988.04'/>
    <!-- bottom-right of left cheekbone -->
        <use xlink:href='#pt' id='pt166' x='461.08' y='1019.47'/>
    <!-- top-right of right cheekbone -->
        <use xlink:href='#pt' id='pt167' x='1006.08' y='927.84'/>
    <!-- centre of right cheekbone -->
        <use xlink:href='#pt' id='pt168' x='954.7' y='988.04'/>
    <!-- bottom-left of right cheekbone -->
        <use xlink:href='#pt' id='pt169' x='888.93' y='1019.47'/>
    <!-- top-left of philtrum -->
        <use xlink:href='#pt' id='pt170' x='648.22' y='1124.24'/>
    <!-- bottom-left of philtrum -->
        <use xlink:href='#pt' id='pt171' x='642.16' y='1159.74'/>
    <!-- top-right of philtrum -->
        <use xlink:href='#pt' id='pt172' x='701.78' y='1124.24'/>
    <!-- bottom-right of philtrum -->
        <use xlink:href='#pt' id='pt173' x='707.84' y='1159.74'/>
    <!-- top of chin dimple -->
        <use xlink:href='#pt' id='pt174' x='675' y='1365.49'/>
    <!-- bottom of chin dimple -->
        <use xlink:href='#pt' id='pt175' x='675' y='1413.88'/>
    <!-- left of horizontal crease under chin -->
        <use xlink:href='#pt' id='pt176' x='615.6' y='1327.72'/>
    <!-- centre of horizontal crease under chin -->
        <use xlink:href='#pt' id='pt177' x='675' y='1315.79'/>
    <!-- right of horizontal crease under chin -->
        <use xlink:href='#pt' id='pt178' x='734.4' y='1327.72'/>
    <!-- right edge of left inner nostril -->
        <use xlink:href='#pt' id='pt179' x='639.34' y='1073.25'/>
    <!-- right edge of septum (divides nostril) -->
        <use xlink:href='#pt' id='pt180' x='656.16' y='1091.79'/>
    <!-- left edge of septum (divides nostril) -->
        <use xlink:href='#pt' id='pt181' x='693.84' y='1091.79'/>
    <!-- left edge of right inner nostril -->
        <use xlink:href='#pt' id='pt182' x='710.66' y='1073.25'/>
    <!-- left of the adams apple -->
        <use xlink:href='#pt' id='pt183' x='569.6' y='1570.01'/>
    <!-- right of the adams apple -->
        <use xlink:href='#pt' id='pt184' x='780.4' y='1570.01'/>
    <!-- upper-middle of where left ear meets face -->
        <use xlink:href='#pt' id='pt185' x='296.34' y='878.18'/>
    <!-- lower-middle of where left ear meets face -->
        <use xlink:href='#pt' id='pt186' x='310.16' y='1006.06'/>
    <!-- upper-middle of where right ear meets face -->
        <use xlink:href='#pt' id='pt187' x='1053.66' y='878.18'/>
    <!-- lower-middle of where right ear meets face -->
        <use xlink:href='#pt' id='pt188' x='1039.84' y='1006.06'/>
</g>
</svg>
EOT;

/* Create a new canvas object and a white image */
$im = new Imagick();
$im->setBackgroundColor(new ImagickPixel('rgba(0,0,0,0)'));
$im->readImageBlob($svg);
$im->setImageFormat("png32");
header("Content-Type: image/png");
echo $im;

?>