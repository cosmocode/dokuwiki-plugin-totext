<?php declare(strict_types=1);

namespace PrinsFrank\Core14FontMetrics;

use Override;

enum Symbol implements Font {
    case space;
    case exclam;
    case universal;
    case numbersign;
    case existential;
    case percent;
    case ampersand;
    case suchthat;
    case parenleft;
    case parenright;
    case asteriskmath;
    case plus;
    case comma;
    case minus;
    case period;
    case slash;
    case zero;
    case one;
    case two;
    case three;
    case four;
    case five;
    case six;
    case seven;
    case eight;
    case nine;
    case colon;
    case semicolon;
    case less;
    case equal;
    case greater;
    case question;
    case congruent;
    case Alpha;
    case Beta;
    case Chi;
    case Delta;
    case Epsilon;
    case Phi;
    case Gamma;
    case Eta;
    case Iota;
    case theta1;
    case Kappa;
    case Lambda;
    case Mu;
    case Nu;
    case Omicron;
    case Pi;
    case Theta;
    case Rho;
    case Sigma;
    case Tau;
    case Upsilon;
    case sigma1;
    case Omega;
    case Xi;
    case Psi;
    case Zeta;
    case bracketleft;
    case therefore;
    case bracketright;
    case perpendicular;
    case underscore;
    case radicalex;
    case alpha;
    case beta;
    case chi;
    case delta;
    case epsilon;
    case phi;
    case gamma;
    case eta;
    case iota;
    case phi1;
    case kappa;
    case lambda;
    case mu;
    case nu;
    case omicron;
    case pi;
    case theta;
    case rho;
    case sigma;
    case tau;
    case upsilon;
    case omega1;
    case omega;
    case xi;
    case psi;
    case zeta;
    case braceleft;
    case bar;
    case braceright;
    case similar;
    case Euro;
    case Upsilon1;
    case minute;
    case lessequal;
    case fraction;
    case infinity;
    case florin;
    case club;
    case diamond;
    case heart;
    case spade;
    case arrowboth;
    case arrowleft;
    case arrowup;
    case arrowright;
    case arrowdown;
    case degree;
    case plusminus;
    case second;
    case greaterequal;
    case multiply;
    case proportional;
    case partialdiff;
    case bullet;
    case divide;
    case notequal;
    case equivalence;
    case approxequal;
    case ellipsis;
    case arrowvertex;
    case arrowhorizex;
    case carriagereturn;
    case aleph;
    case Ifraktur;
    case Rfraktur;
    case weierstrass;
    case circlemultiply;
    case circleplus;
    case emptyset;
    case intersection;
    case union;
    case propersuperset;
    case reflexsuperset;
    case notsubset;
    case propersubset;
    case reflexsubset;
    case element;
    case notelement;
    case angle;
    case gradient;
    case registerserif;
    case copyrightserif;
    case trademarkserif;
    case product;
    case radical;
    case dotmath;
    case logicalnot;
    case logicaland;
    case logicalor;
    case arrowdblboth;
    case arrowdblleft;
    case arrowdblup;
    case arrowdblright;
    case arrowdbldown;
    case lozenge;
    case angleleft;
    case registersans;
    case copyrightsans;
    case trademarksans;
    case summation;
    case parenlefttp;
    case parenleftex;
    case parenleftbt;
    case bracketlefttp;
    case bracketleftex;
    case bracketleftbt;
    case bracelefttp;
    case braceleftmid;
    case braceleftbt;
    case braceex;
    case angleright;
    case integral;
    case integraltp;
    case integralex;
    case integralbt;
    case parenrighttp;
    case parenrightex;
    case parenrightbt;
    case bracketrighttp;
    case bracketrightex;
    case bracketrightbt;
    case bracerighttp;
    case bracerightmid;
    case bracerightbt;
    case apple;

    #[Override]
    public function getWidth(): float {
        return match ($this) {
            self::space => 0.250,
            self::exclam => 0.333,
            self::universal => 0.713,
            self::numbersign => 0.500,
            self::existential => 0.549,
            self::percent => 0.833,
            self::ampersand => 0.778,
            self::suchthat => 0.439,
            self::parenleft => 0.333,
            self::parenright => 0.333,
            self::asteriskmath => 0.500,
            self::plus => 0.549,
            self::comma => 0.250,
            self::minus => 0.549,
            self::period => 0.250,
            self::slash => 0.278,
            self::zero => 0.500,
            self::one => 0.500,
            self::two => 0.500,
            self::three => 0.500,
            self::four => 0.500,
            self::five => 0.500,
            self::six => 0.500,
            self::seven => 0.500,
            self::eight => 0.500,
            self::nine => 0.500,
            self::colon => 0.278,
            self::semicolon => 0.278,
            self::less => 0.549,
            self::equal => 0.549,
            self::greater => 0.549,
            self::question => 0.444,
            self::congruent => 0.549,
            self::Alpha => 0.722,
            self::Beta => 0.667,
            self::Chi => 0.722,
            self::Delta => 0.612,
            self::Epsilon => 0.611,
            self::Phi => 0.763,
            self::Gamma => 0.603,
            self::Eta => 0.722,
            self::Iota => 0.333,
            self::theta1 => 0.631,
            self::Kappa => 0.722,
            self::Lambda => 0.686,
            self::Mu => 0.889,
            self::Nu => 0.722,
            self::Omicron => 0.722,
            self::Pi => 0.768,
            self::Theta => 0.741,
            self::Rho => 0.556,
            self::Sigma => 0.592,
            self::Tau => 0.611,
            self::Upsilon => 0.690,
            self::sigma1 => 0.439,
            self::Omega => 0.768,
            self::Xi => 0.645,
            self::Psi => 0.795,
            self::Zeta => 0.611,
            self::bracketleft => 0.333,
            self::therefore => 0.863,
            self::bracketright => 0.333,
            self::perpendicular => 0.658,
            self::underscore => 0.500,
            self::radicalex => 0.500,
            self::alpha => 0.631,
            self::beta => 0.549,
            self::chi => 0.549,
            self::delta => 0.494,
            self::epsilon => 0.439,
            self::phi => 0.521,
            self::gamma => 0.411,
            self::eta => 0.603,
            self::iota => 0.329,
            self::phi1 => 0.603,
            self::kappa => 0.549,
            self::lambda => 0.549,
            self::mu => 0.576,
            self::nu => 0.521,
            self::omicron => 0.549,
            self::pi => 0.549,
            self::theta => 0.521,
            self::rho => 0.549,
            self::sigma => 0.603,
            self::tau => 0.439,
            self::upsilon => 0.576,
            self::omega1 => 0.713,
            self::omega => 0.686,
            self::xi => 0.493,
            self::psi => 0.686,
            self::zeta => 0.494,
            self::braceleft => 0.480,
            self::bar => 0.200,
            self::braceright => 0.480,
            self::similar => 0.549,
            self::Euro => 0.750,
            self::Upsilon1 => 0.620,
            self::minute => 0.247,
            self::lessequal => 0.549,
            self::fraction => 0.167,
            self::infinity => 0.713,
            self::florin => 0.500,
            self::club => 0.753,
            self::diamond => 0.753,
            self::heart => 0.753,
            self::spade => 0.753,
            self::arrowboth => 0.1042,
            self::arrowleft => 0.987,
            self::arrowup => 0.603,
            self::arrowright => 0.987,
            self::arrowdown => 0.603,
            self::degree => 0.400,
            self::plusminus => 0.549,
            self::second => 0.411,
            self::greaterequal => 0.549,
            self::multiply => 0.549,
            self::proportional => 0.713,
            self::partialdiff => 0.494,
            self::bullet => 0.460,
            self::divide => 0.549,
            self::notequal => 0.549,
            self::equivalence => 0.549,
            self::approxequal => 0.549,
            self::ellipsis => 0.1000,
            self::arrowvertex => 0.603,
            self::arrowhorizex => 0.1000,
            self::carriagereturn => 0.658,
            self::aleph => 0.823,
            self::Ifraktur => 0.686,
            self::Rfraktur => 0.795,
            self::weierstrass => 0.987,
            self::circlemultiply => 0.768,
            self::circleplus => 0.768,
            self::emptyset => 0.823,
            self::intersection => 0.768,
            self::union => 0.768,
            self::propersuperset => 0.713,
            self::reflexsuperset => 0.713,
            self::notsubset => 0.713,
            self::propersubset => 0.713,
            self::reflexsubset => 0.713,
            self::element => 0.713,
            self::notelement => 0.713,
            self::angle => 0.768,
            self::gradient => 0.713,
            self::registerserif => 0.790,
            self::copyrightserif => 0.790,
            self::trademarkserif => 0.890,
            self::product => 0.823,
            self::radical => 0.549,
            self::dotmath => 0.250,
            self::logicalnot => 0.713,
            self::logicaland => 0.603,
            self::logicalor => 0.603,
            self::arrowdblboth => 0.1042,
            self::arrowdblleft => 0.987,
            self::arrowdblup => 0.603,
            self::arrowdblright => 0.987,
            self::arrowdbldown => 0.603,
            self::lozenge => 0.494,
            self::angleleft => 0.329,
            self::registersans => 0.790,
            self::copyrightsans => 0.790,
            self::trademarksans => 0.786,
            self::summation => 0.713,
            self::parenlefttp => 0.384,
            self::parenleftex => 0.384,
            self::parenleftbt => 0.384,
            self::bracketlefttp => 0.384,
            self::bracketleftex => 0.384,
            self::bracketleftbt => 0.384,
            self::bracelefttp => 0.494,
            self::braceleftmid => 0.494,
            self::braceleftbt => 0.494,
            self::braceex => 0.494,
            self::angleright => 0.329,
            self::integral => 0.274,
            self::integraltp => 0.686,
            self::integralex => 0.686,
            self::integralbt => 0.686,
            self::parenrighttp => 0.384,
            self::parenrightex => 0.384,
            self::parenrightbt => 0.384,
            self::bracketrighttp => 0.384,
            self::bracketrightex => 0.384,
            self::bracketrightbt => 0.384,
            self::bracerighttp => 0.494,
            self::bracerightmid => 0.494,
            self::bracerightbt => 0.494,
            self::apple => 0.790,
        };
    }

    #[Override]
    public static function fromCharacterCode(int $code): ?self {
        return match ($code) {
            32 => self::space,
            33 => self::exclam,
            34 => self::universal,
            35 => self::numbersign,
            36 => self::existential,
            37 => self::percent,
            38 => self::ampersand,
            39 => self::suchthat,
            40 => self::parenleft,
            41 => self::parenright,
            42 => self::asteriskmath,
            43 => self::plus,
            44 => self::comma,
            45 => self::minus,
            46 => self::period,
            47 => self::slash,
            48 => self::zero,
            49 => self::one,
            50 => self::two,
            51 => self::three,
            52 => self::four,
            53 => self::five,
            54 => self::six,
            55 => self::seven,
            56 => self::eight,
            57 => self::nine,
            58 => self::colon,
            59 => self::semicolon,
            60 => self::less,
            61 => self::equal,
            62 => self::greater,
            63 => self::question,
            64 => self::congruent,
            65 => self::Alpha,
            66 => self::Beta,
            67 => self::Chi,
            68 => self::Delta,
            69 => self::Epsilon,
            70 => self::Phi,
            71 => self::Gamma,
            72 => self::Eta,
            73 => self::Iota,
            74 => self::theta1,
            75 => self::Kappa,
            76 => self::Lambda,
            77 => self::Mu,
            78 => self::Nu,
            79 => self::Omicron,
            80 => self::Pi,
            81 => self::Theta,
            82 => self::Rho,
            83 => self::Sigma,
            84 => self::Tau,
            85 => self::Upsilon,
            86 => self::sigma1,
            87 => self::Omega,
            88 => self::Xi,
            89 => self::Psi,
            90 => self::Zeta,
            91 => self::bracketleft,
            92 => self::therefore,
            93 => self::bracketright,
            94 => self::perpendicular,
            95 => self::underscore,
            96 => self::radicalex,
            97 => self::alpha,
            98 => self::beta,
            99 => self::chi,
            100 => self::delta,
            101 => self::epsilon,
            102 => self::phi,
            103 => self::gamma,
            104 => self::eta,
            105 => self::iota,
            106 => self::phi1,
            107 => self::kappa,
            108 => self::lambda,
            109 => self::mu,
            110 => self::nu,
            111 => self::omicron,
            112 => self::pi,
            113 => self::theta,
            114 => self::rho,
            115 => self::sigma,
            116 => self::tau,
            117 => self::upsilon,
            118 => self::omega1,
            119 => self::omega,
            120 => self::xi,
            121 => self::psi,
            122 => self::zeta,
            123 => self::braceleft,
            124 => self::bar,
            125 => self::braceright,
            126 => self::similar,
            160 => self::Euro,
            161 => self::Upsilon1,
            162 => self::minute,
            163 => self::lessequal,
            164 => self::fraction,
            165 => self::infinity,
            166 => self::florin,
            167 => self::club,
            168 => self::diamond,
            169 => self::heart,
            170 => self::spade,
            171 => self::arrowboth,
            172 => self::arrowleft,
            173 => self::arrowup,
            174 => self::arrowright,
            175 => self::arrowdown,
            176 => self::degree,
            177 => self::plusminus,
            178 => self::second,
            179 => self::greaterequal,
            180 => self::multiply,
            181 => self::proportional,
            182 => self::partialdiff,
            183 => self::bullet,
            184 => self::divide,
            185 => self::notequal,
            186 => self::equivalence,
            187 => self::approxequal,
            188 => self::ellipsis,
            189 => self::arrowvertex,
            190 => self::arrowhorizex,
            191 => self::carriagereturn,
            192 => self::aleph,
            193 => self::Ifraktur,
            194 => self::Rfraktur,
            195 => self::weierstrass,
            196 => self::circlemultiply,
            197 => self::circleplus,
            198 => self::emptyset,
            199 => self::intersection,
            200 => self::union,
            201 => self::propersuperset,
            202 => self::reflexsuperset,
            203 => self::notsubset,
            204 => self::propersubset,
            205 => self::reflexsubset,
            206 => self::element,
            207 => self::notelement,
            208 => self::angle,
            209 => self::gradient,
            210 => self::registerserif,
            211 => self::copyrightserif,
            212 => self::trademarkserif,
            213 => self::product,
            214 => self::radical,
            215 => self::dotmath,
            216 => self::logicalnot,
            217 => self::logicaland,
            218 => self::logicalor,
            219 => self::arrowdblboth,
            220 => self::arrowdblleft,
            221 => self::arrowdblup,
            222 => self::arrowdblright,
            223 => self::arrowdbldown,
            224 => self::lozenge,
            225 => self::angleleft,
            226 => self::registersans,
            227 => self::copyrightsans,
            228 => self::trademarksans,
            229 => self::summation,
            230 => self::parenlefttp,
            231 => self::parenleftex,
            232 => self::parenleftbt,
            233 => self::bracketlefttp,
            234 => self::bracketleftex,
            235 => self::bracketleftbt,
            236 => self::bracelefttp,
            237 => self::braceleftmid,
            238 => self::braceleftbt,
            239 => self::braceex,
            241 => self::angleright,
            242 => self::integral,
            243 => self::integraltp,
            244 => self::integralex,
            245 => self::integralbt,
            246 => self::parenrighttp,
            247 => self::parenrightex,
            248 => self::parenrightbt,
            249 => self::bracketrighttp,
            250 => self::bracketrightex,
            251 => self::bracketrightbt,
            252 => self::bracerighttp,
            253 => self::bracerightmid,
            254 => self::bracerightbt,
            default => null,
        };
    }
}
