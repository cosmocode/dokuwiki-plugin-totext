<?php declare(strict_types=1);

namespace PrinsFrank\Core14FontMetrics;

use Override;

enum ZapfDingbats implements Font {
    case space;
    case a1;
    case a2;
    case a202;
    case a3;
    case a4;
    case a5;
    case a119;
    case a118;
    case a117;
    case a11;
    case a12;
    case a13;
    case a14;
    case a15;
    case a16;
    case a105;
    case a17;
    case a18;
    case a19;
    case a20;
    case a21;
    case a22;
    case a23;
    case a24;
    case a25;
    case a26;
    case a27;
    case a28;
    case a6;
    case a7;
    case a8;
    case a9;
    case a10;
    case a29;
    case a30;
    case a31;
    case a32;
    case a33;
    case a34;
    case a35;
    case a36;
    case a37;
    case a38;
    case a39;
    case a40;
    case a41;
    case a42;
    case a43;
    case a44;
    case a45;
    case a46;
    case a47;
    case a48;
    case a49;
    case a50;
    case a51;
    case a52;
    case a53;
    case a54;
    case a55;
    case a56;
    case a57;
    case a58;
    case a59;
    case a60;
    case a61;
    case a62;
    case a63;
    case a64;
    case a65;
    case a66;
    case a67;
    case a68;
    case a69;
    case a70;
    case a71;
    case a72;
    case a73;
    case a74;
    case a203;
    case a75;
    case a204;
    case a76;
    case a77;
    case a78;
    case a79;
    case a81;
    case a82;
    case a83;
    case a84;
    case a97;
    case a98;
    case a99;
    case a100;
    case a89;
    case a90;
    case a93;
    case a94;
    case a91;
    case a92;
    case a205;
    case a85;
    case a206;
    case a86;
    case a87;
    case a88;
    case a95;
    case a96;
    case a101;
    case a102;
    case a103;
    case a104;
    case a106;
    case a107;
    case a108;
    case a112;
    case a111;
    case a110;
    case a109;
    case a120;
    case a121;
    case a122;
    case a123;
    case a124;
    case a125;
    case a126;
    case a127;
    case a128;
    case a129;
    case a130;
    case a131;
    case a132;
    case a133;
    case a134;
    case a135;
    case a136;
    case a137;
    case a138;
    case a139;
    case a140;
    case a141;
    case a142;
    case a143;
    case a144;
    case a145;
    case a146;
    case a147;
    case a148;
    case a149;
    case a150;
    case a151;
    case a152;
    case a153;
    case a154;
    case a155;
    case a156;
    case a157;
    case a158;
    case a159;
    case a160;
    case a161;
    case a163;
    case a164;
    case a196;
    case a165;
    case a192;
    case a166;
    case a167;
    case a168;
    case a169;
    case a170;
    case a171;
    case a172;
    case a173;
    case a162;
    case a174;
    case a175;
    case a176;
    case a177;
    case a178;
    case a179;
    case a193;
    case a180;
    case a199;
    case a181;
    case a200;
    case a182;
    case a201;
    case a183;
    case a184;
    case a197;
    case a185;
    case a194;
    case a198;
    case a186;
    case a195;
    case a187;
    case a188;
    case a189;
    case a190;
    case a191;

    #[Override]
    public function getWidth(): float {
        return match ($this) {
            self::space => 0.278,
            self::a1 => 0.974,
            self::a2 => 0.961,
            self::a202 => 0.974,
            self::a3 => 0.980,
            self::a4 => 0.719,
            self::a5 => 0.789,
            self::a119 => 0.790,
            self::a118 => 0.791,
            self::a117 => 0.690,
            self::a11 => 0.960,
            self::a12 => 0.939,
            self::a13 => 0.549,
            self::a14 => 0.855,
            self::a15 => 0.911,
            self::a16 => 0.933,
            self::a105 => 0.911,
            self::a17 => 0.945,
            self::a18 => 0.974,
            self::a19 => 0.755,
            self::a20 => 0.846,
            self::a21 => 0.762,
            self::a22 => 0.761,
            self::a23 => 0.571,
            self::a24 => 0.677,
            self::a25 => 0.763,
            self::a26 => 0.760,
            self::a27 => 0.759,
            self::a28 => 0.754,
            self::a6 => 0.494,
            self::a7 => 0.552,
            self::a8 => 0.537,
            self::a9 => 0.577,
            self::a10 => 0.692,
            self::a29 => 0.786,
            self::a30 => 0.788,
            self::a31 => 0.788,
            self::a32 => 0.790,
            self::a33 => 0.793,
            self::a34 => 0.794,
            self::a35 => 0.816,
            self::a36 => 0.823,
            self::a37 => 0.789,
            self::a38 => 0.841,
            self::a39 => 0.823,
            self::a40 => 0.833,
            self::a41 => 0.816,
            self::a42 => 0.831,
            self::a43 => 0.923,
            self::a44 => 0.744,
            self::a45 => 0.723,
            self::a46 => 0.749,
            self::a47 => 0.790,
            self::a48 => 0.792,
            self::a49 => 0.695,
            self::a50 => 0.776,
            self::a51 => 0.768,
            self::a52 => 0.792,
            self::a53 => 0.759,
            self::a54 => 0.707,
            self::a55 => 0.708,
            self::a56 => 0.682,
            self::a57 => 0.701,
            self::a58 => 0.826,
            self::a59 => 0.815,
            self::a60 => 0.789,
            self::a61 => 0.789,
            self::a62 => 0.707,
            self::a63 => 0.687,
            self::a64 => 0.696,
            self::a65 => 0.689,
            self::a66 => 0.786,
            self::a67 => 0.787,
            self::a68 => 0.713,
            self::a69 => 0.791,
            self::a70 => 0.785,
            self::a71 => 0.791,
            self::a72 => 0.873,
            self::a73 => 0.761,
            self::a74 => 0.762,
            self::a203 => 0.762,
            self::a75 => 0.759,
            self::a204 => 0.759,
            self::a76 => 0.892,
            self::a77 => 0.892,
            self::a78 => 0.788,
            self::a79 => 0.784,
            self::a81 => 0.438,
            self::a82 => 0.138,
            self::a83 => 0.277,
            self::a84 => 0.415,
            self::a97 => 0.392,
            self::a98 => 0.392,
            self::a99 => 0.668,
            self::a100 => 0.668,
            self::a89 => 0.390,
            self::a90 => 0.390,
            self::a93 => 0.317,
            self::a94 => 0.317,
            self::a91 => 0.276,
            self::a92 => 0.276,
            self::a205 => 0.509,
            self::a85 => 0.509,
            self::a206 => 0.410,
            self::a86 => 0.410,
            self::a87 => 0.234,
            self::a88 => 0.234,
            self::a95 => 0.334,
            self::a96 => 0.334,
            self::a101 => 0.732,
            self::a102 => 0.544,
            self::a103 => 0.544,
            self::a104 => 0.910,
            self::a106 => 0.667,
            self::a107 => 0.760,
            self::a108 => 0.760,
            self::a112 => 0.776,
            self::a111 => 0.595,
            self::a110 => 0.694,
            self::a109 => 0.626,
            self::a120 => 0.788,
            self::a121 => 0.788,
            self::a122 => 0.788,
            self::a123 => 0.788,
            self::a124 => 0.788,
            self::a125 => 0.788,
            self::a126 => 0.788,
            self::a127 => 0.788,
            self::a128 => 0.788,
            self::a129 => 0.788,
            self::a130 => 0.788,
            self::a131 => 0.788,
            self::a132 => 0.788,
            self::a133 => 0.788,
            self::a134 => 0.788,
            self::a135 => 0.788,
            self::a136 => 0.788,
            self::a137 => 0.788,
            self::a138 => 0.788,
            self::a139 => 0.788,
            self::a140 => 0.788,
            self::a141 => 0.788,
            self::a142 => 0.788,
            self::a143 => 0.788,
            self::a144 => 0.788,
            self::a145 => 0.788,
            self::a146 => 0.788,
            self::a147 => 0.788,
            self::a148 => 0.788,
            self::a149 => 0.788,
            self::a150 => 0.788,
            self::a151 => 0.788,
            self::a152 => 0.788,
            self::a153 => 0.788,
            self::a154 => 0.788,
            self::a155 => 0.788,
            self::a156 => 0.788,
            self::a157 => 0.788,
            self::a158 => 0.788,
            self::a159 => 0.788,
            self::a160 => 0.894,
            self::a161 => 0.838,
            self::a163 => 0.1016,
            self::a164 => 0.458,
            self::a196 => 0.748,
            self::a165 => 0.924,
            self::a192 => 0.748,
            self::a166 => 0.918,
            self::a167 => 0.927,
            self::a168 => 0.928,
            self::a169 => 0.928,
            self::a170 => 0.834,
            self::a171 => 0.873,
            self::a172 => 0.828,
            self::a173 => 0.924,
            self::a162 => 0.924,
            self::a174 => 0.917,
            self::a175 => 0.930,
            self::a176 => 0.931,
            self::a177 => 0.463,
            self::a178 => 0.883,
            self::a179 => 0.836,
            self::a193 => 0.836,
            self::a180 => 0.867,
            self::a199 => 0.867,
            self::a181 => 0.696,
            self::a200 => 0.696,
            self::a182 => 0.874,
            self::a201 => 0.874,
            self::a183 => 0.760,
            self::a184 => 0.946,
            self::a197 => 0.771,
            self::a185 => 0.865,
            self::a194 => 0.771,
            self::a198 => 0.888,
            self::a186 => 0.967,
            self::a195 => 0.888,
            self::a187 => 0.831,
            self::a188 => 0.873,
            self::a189 => 0.927,
            self::a190 => 0.970,
            self::a191 => 0.918,
        };
    }

    #[Override]
    public static function fromCharacterCode(int $code): ?self {
        return match ($code) {
            32 => self::space,
            33 => self::a1,
            34 => self::a2,
            35 => self::a202,
            36 => self::a3,
            37 => self::a4,
            38 => self::a5,
            39 => self::a119,
            40 => self::a118,
            41 => self::a117,
            42 => self::a11,
            43 => self::a12,
            44 => self::a13,
            45 => self::a14,
            46 => self::a15,
            47 => self::a16,
            48 => self::a105,
            49 => self::a17,
            50 => self::a18,
            51 => self::a19,
            52 => self::a20,
            53 => self::a21,
            54 => self::a22,
            55 => self::a23,
            56 => self::a24,
            57 => self::a25,
            58 => self::a26,
            59 => self::a27,
            60 => self::a28,
            61 => self::a6,
            62 => self::a7,
            63 => self::a8,
            64 => self::a9,
            65 => self::a10,
            66 => self::a29,
            67 => self::a30,
            68 => self::a31,
            69 => self::a32,
            70 => self::a33,
            71 => self::a34,
            72 => self::a35,
            73 => self::a36,
            74 => self::a37,
            75 => self::a38,
            76 => self::a39,
            77 => self::a40,
            78 => self::a41,
            79 => self::a42,
            80 => self::a43,
            81 => self::a44,
            82 => self::a45,
            83 => self::a46,
            84 => self::a47,
            85 => self::a48,
            86 => self::a49,
            87 => self::a50,
            88 => self::a51,
            89 => self::a52,
            90 => self::a53,
            91 => self::a54,
            92 => self::a55,
            93 => self::a56,
            94 => self::a57,
            95 => self::a58,
            96 => self::a59,
            97 => self::a60,
            98 => self::a61,
            99 => self::a62,
            100 => self::a63,
            101 => self::a64,
            102 => self::a65,
            103 => self::a66,
            104 => self::a67,
            105 => self::a68,
            106 => self::a69,
            107 => self::a70,
            108 => self::a71,
            109 => self::a72,
            110 => self::a73,
            111 => self::a74,
            112 => self::a203,
            113 => self::a75,
            114 => self::a204,
            115 => self::a76,
            116 => self::a77,
            117 => self::a78,
            118 => self::a79,
            119 => self::a81,
            120 => self::a82,
            121 => self::a83,
            122 => self::a84,
            123 => self::a97,
            124 => self::a98,
            125 => self::a99,
            126 => self::a100,
            128 => self::a89,
            129 => self::a90,
            130 => self::a93,
            131 => self::a94,
            132 => self::a91,
            133 => self::a92,
            134 => self::a205,
            135 => self::a85,
            136 => self::a206,
            137 => self::a86,
            138 => self::a87,
            139 => self::a88,
            140 => self::a95,
            141 => self::a96,
            161 => self::a101,
            162 => self::a102,
            163 => self::a103,
            164 => self::a104,
            165 => self::a106,
            166 => self::a107,
            167 => self::a108,
            168 => self::a112,
            169 => self::a111,
            170 => self::a110,
            171 => self::a109,
            172 => self::a120,
            173 => self::a121,
            174 => self::a122,
            175 => self::a123,
            176 => self::a124,
            177 => self::a125,
            178 => self::a126,
            179 => self::a127,
            180 => self::a128,
            181 => self::a129,
            182 => self::a130,
            183 => self::a131,
            184 => self::a132,
            185 => self::a133,
            186 => self::a134,
            187 => self::a135,
            188 => self::a136,
            189 => self::a137,
            190 => self::a138,
            191 => self::a139,
            192 => self::a140,
            193 => self::a141,
            194 => self::a142,
            195 => self::a143,
            196 => self::a144,
            197 => self::a145,
            198 => self::a146,
            199 => self::a147,
            200 => self::a148,
            201 => self::a149,
            202 => self::a150,
            203 => self::a151,
            204 => self::a152,
            205 => self::a153,
            206 => self::a154,
            207 => self::a155,
            208 => self::a156,
            209 => self::a157,
            210 => self::a158,
            211 => self::a159,
            212 => self::a160,
            213 => self::a161,
            214 => self::a163,
            215 => self::a164,
            216 => self::a196,
            217 => self::a165,
            218 => self::a192,
            219 => self::a166,
            220 => self::a167,
            221 => self::a168,
            222 => self::a169,
            223 => self::a170,
            224 => self::a171,
            225 => self::a172,
            226 => self::a173,
            227 => self::a162,
            228 => self::a174,
            229 => self::a175,
            230 => self::a176,
            231 => self::a177,
            232 => self::a178,
            233 => self::a179,
            234 => self::a193,
            235 => self::a180,
            236 => self::a199,
            237 => self::a181,
            238 => self::a200,
            239 => self::a182,
            241 => self::a201,
            242 => self::a183,
            243 => self::a184,
            244 => self::a197,
            245 => self::a185,
            246 => self::a194,
            247 => self::a198,
            248 => self::a186,
            249 => self::a195,
            250 => self::a187,
            251 => self::a188,
            252 => self::a189,
            253 => self::a190,
            254 => self::a191,
            default => null,
        };
    }
}
