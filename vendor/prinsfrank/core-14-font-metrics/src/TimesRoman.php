<?php declare(strict_types=1);

namespace PrinsFrank\Core14FontMetrics;

use Override;

enum TimesRoman implements Font {
    case space;
    case exclam;
    case quotedbl;
    case numbersign;
    case dollar;
    case percent;
    case ampersand;
    case quoteright;
    case parenleft;
    case parenright;
    case asterisk;
    case plus;
    case comma;
    case hyphen;
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
    case at;
    case A;
    case B;
    case C;
    case D;
    case E;
    case F;
    case G;
    case H;
    case I;
    case J;
    case K;
    case L;
    case M;
    case N;
    case O;
    case P;
    case Q;
    case R;
    case S;
    case T;
    case U;
    case V;
    case W;
    case X;
    case Y;
    case Z;
    case bracketleft;
    case backslash;
    case bracketright;
    case asciicircum;
    case underscore;
    case quoteleft;
    case a;
    case b;
    case c;
    case d;
    case e;
    case f;
    case g;
    case h;
    case i;
    case j;
    case k;
    case l;
    case m;
    case n;
    case o;
    case p;
    case q;
    case r;
    case s;
    case t;
    case u;
    case v;
    case w;
    case x;
    case y;
    case z;
    case braceleft;
    case bar;
    case braceright;
    case asciitilde;
    case exclamdown;
    case cent;
    case sterling;
    case fraction;
    case yen;
    case florin;
    case section;
    case currency;
    case quotesingle;
    case quotedblleft;
    case guillemotleft;
    case guilsinglleft;
    case guilsinglright;
    case fi;
    case fl;
    case endash;
    case dagger;
    case daggerdbl;
    case periodcentered;
    case paragraph;
    case bullet;
    case quotesinglbase;
    case quotedblbase;
    case quotedblright;
    case guillemotright;
    case ellipsis;
    case perthousand;
    case questiondown;
    case grave;
    case acute;
    case circumflex;
    case tilde;
    case macron;
    case breve;
    case dotaccent;
    case dieresis;
    case ring;
    case cedilla;
    case hungarumlaut;
    case ogonek;
    case caron;
    case emdash;
    case AE;
    case ordfeminine;
    case Lslash;
    case Oslash;
    case OE;
    case ordmasculine;
    case ae;
    case dotlessi;
    case lslash;
    case oslash;
    case oe;
    case germandbls;
    case Idieresis;
    case eacute;
    case abreve;
    case uhungarumlaut;
    case ecaron;
    case Ydieresis;
    case divide;
    case Yacute;
    case Acircumflex;
    case aacute;
    case Ucircumflex;
    case yacute;
    case scommaaccent;
    case ecircumflex;
    case Uring;
    case Udieresis;
    case aogonek;
    case Uacute;
    case uogonek;
    case Edieresis;
    case Dcroat;
    case commaaccent;
    case copyright;
    case Emacron;
    case ccaron;
    case aring;
    case Ncommaaccent;
    case lacute;
    case agrave;
    case Tcommaaccent;
    case Cacute;
    case atilde;
    case Edotaccent;
    case scaron;
    case scedilla;
    case iacute;
    case lozenge;
    case Rcaron;
    case Gcommaaccent;
    case ucircumflex;
    case acircumflex;
    case Amacron;
    case rcaron;
    case ccedilla;
    case Zdotaccent;
    case Thorn;
    case Omacron;
    case Racute;
    case Sacute;
    case dcaron;
    case Umacron;
    case uring;
    case threesuperior;
    case Ograve;
    case Agrave;
    case Abreve;
    case multiply;
    case uacute;
    case Tcaron;
    case partialdiff;
    case ydieresis;
    case Nacute;
    case icircumflex;
    case Ecircumflex;
    case adieresis;
    case edieresis;
    case cacute;
    case nacute;
    case umacron;
    case Ncaron;
    case Iacute;
    case plusminus;
    case brokenbar;
    case registered;
    case Gbreve;
    case Idotaccent;
    case summation;
    case Egrave;
    case racute;
    case omacron;
    case Zacute;
    case Zcaron;
    case greaterequal;
    case Eth;
    case Ccedilla;
    case lcommaaccent;
    case tcaron;
    case eogonek;
    case Uogonek;
    case Aacute;
    case Adieresis;
    case egrave;
    case zacute;
    case iogonek;
    case Oacute;
    case oacute;
    case amacron;
    case sacute;
    case idieresis;
    case Ocircumflex;
    case Ugrave;
    case Delta;
    case thorn;
    case twosuperior;
    case Odieresis;
    case mu;
    case igrave;
    case ohungarumlaut;
    case Eogonek;
    case dcroat;
    case threequarters;
    case Scedilla;
    case lcaron;
    case Kcommaaccent;
    case Lacute;
    case trademark;
    case edotaccent;
    case Igrave;
    case Imacron;
    case Lcaron;
    case onehalf;
    case lessequal;
    case ocircumflex;
    case ntilde;
    case Uhungarumlaut;
    case Eacute;
    case emacron;
    case gbreve;
    case onequarter;
    case Scaron;
    case Scommaaccent;
    case Ohungarumlaut;
    case degree;
    case ograve;
    case Ccaron;
    case ugrave;
    case radical;
    case Dcaron;
    case rcommaaccent;
    case Ntilde;
    case otilde;
    case Rcommaaccent;
    case Lcommaaccent;
    case Atilde;
    case Aogonek;
    case Aring;
    case Otilde;
    case zdotaccent;
    case Ecaron;
    case Iogonek;
    case kcommaaccent;
    case minus;
    case Icircumflex;
    case ncaron;
    case tcommaaccent;
    case logicalnot;
    case odieresis;
    case udieresis;
    case notequal;
    case gcommaaccent;
    case eth;
    case zcaron;
    case ncommaaccent;
    case onesuperior;
    case imacron;
    case Euro;

    #[Override]
    public function getWidth(): float {
        return match ($this) {
            self::space => 0.250,
            self::exclam => 0.333,
            self::quotedbl => 0.408,
            self::numbersign => 0.500,
            self::dollar => 0.500,
            self::percent => 0.833,
            self::ampersand => 0.778,
            self::quoteright => 0.333,
            self::parenleft => 0.333,
            self::parenright => 0.333,
            self::asterisk => 0.500,
            self::plus => 0.564,
            self::comma => 0.250,
            self::hyphen => 0.333,
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
            self::less => 0.564,
            self::equal => 0.564,
            self::greater => 0.564,
            self::question => 0.444,
            self::at => 0.921,
            self::A => 0.722,
            self::B => 0.667,
            self::C => 0.667,
            self::D => 0.722,
            self::E => 0.611,
            self::F => 0.556,
            self::G => 0.722,
            self::H => 0.722,
            self::I => 0.333,
            self::J => 0.389,
            self::K => 0.722,
            self::L => 0.611,
            self::M => 0.889,
            self::N => 0.722,
            self::O => 0.722,
            self::P => 0.556,
            self::Q => 0.722,
            self::R => 0.667,
            self::S => 0.556,
            self::T => 0.611,
            self::U => 0.722,
            self::V => 0.722,
            self::W => 0.944,
            self::X => 0.722,
            self::Y => 0.722,
            self::Z => 0.611,
            self::bracketleft => 0.333,
            self::backslash => 0.278,
            self::bracketright => 0.333,
            self::asciicircum => 0.469,
            self::underscore => 0.500,
            self::quoteleft => 0.333,
            self::a => 0.444,
            self::b => 0.500,
            self::c => 0.444,
            self::d => 0.500,
            self::e => 0.444,
            self::f => 0.333,
            self::g => 0.500,
            self::h => 0.500,
            self::i => 0.278,
            self::j => 0.278,
            self::k => 0.500,
            self::l => 0.278,
            self::m => 0.778,
            self::n => 0.500,
            self::o => 0.500,
            self::p => 0.500,
            self::q => 0.500,
            self::r => 0.333,
            self::s => 0.389,
            self::t => 0.278,
            self::u => 0.500,
            self::v => 0.500,
            self::w => 0.722,
            self::x => 0.500,
            self::y => 0.500,
            self::z => 0.444,
            self::braceleft => 0.480,
            self::bar => 0.200,
            self::braceright => 0.480,
            self::asciitilde => 0.541,
            self::exclamdown => 0.333,
            self::cent => 0.500,
            self::sterling => 0.500,
            self::fraction => 0.167,
            self::yen => 0.500,
            self::florin => 0.500,
            self::section => 0.500,
            self::currency => 0.500,
            self::quotesingle => 0.180,
            self::quotedblleft => 0.444,
            self::guillemotleft => 0.500,
            self::guilsinglleft => 0.333,
            self::guilsinglright => 0.333,
            self::fi => 0.556,
            self::fl => 0.556,
            self::endash => 0.500,
            self::dagger => 0.500,
            self::daggerdbl => 0.500,
            self::periodcentered => 0.250,
            self::paragraph => 0.453,
            self::bullet => 0.350,
            self::quotesinglbase => 0.333,
            self::quotedblbase => 0.444,
            self::quotedblright => 0.444,
            self::guillemotright => 0.500,
            self::ellipsis => 0.1000,
            self::perthousand => 0.1000,
            self::questiondown => 0.444,
            self::grave => 0.333,
            self::acute => 0.333,
            self::circumflex => 0.333,
            self::tilde => 0.333,
            self::macron => 0.333,
            self::breve => 0.333,
            self::dotaccent => 0.333,
            self::dieresis => 0.333,
            self::ring => 0.333,
            self::cedilla => 0.333,
            self::hungarumlaut => 0.333,
            self::ogonek => 0.333,
            self::caron => 0.333,
            self::emdash => 0.1000,
            self::AE => 0.889,
            self::ordfeminine => 0.276,
            self::Lslash => 0.611,
            self::Oslash => 0.722,
            self::OE => 0.889,
            self::ordmasculine => 0.310,
            self::ae => 0.667,
            self::dotlessi => 0.278,
            self::lslash => 0.278,
            self::oslash => 0.500,
            self::oe => 0.722,
            self::germandbls => 0.500,
            self::Idieresis => 0.333,
            self::eacute => 0.444,
            self::abreve => 0.444,
            self::uhungarumlaut => 0.500,
            self::ecaron => 0.444,
            self::Ydieresis => 0.722,
            self::divide => 0.564,
            self::Yacute => 0.722,
            self::Acircumflex => 0.722,
            self::aacute => 0.444,
            self::Ucircumflex => 0.722,
            self::yacute => 0.500,
            self::scommaaccent => 0.389,
            self::ecircumflex => 0.444,
            self::Uring => 0.722,
            self::Udieresis => 0.722,
            self::aogonek => 0.444,
            self::Uacute => 0.722,
            self::uogonek => 0.500,
            self::Edieresis => 0.611,
            self::Dcroat => 0.722,
            self::commaaccent => 0.250,
            self::copyright => 0.760,
            self::Emacron => 0.611,
            self::ccaron => 0.444,
            self::aring => 0.444,
            self::Ncommaaccent => 0.722,
            self::lacute => 0.278,
            self::agrave => 0.444,
            self::Tcommaaccent => 0.611,
            self::Cacute => 0.667,
            self::atilde => 0.444,
            self::Edotaccent => 0.611,
            self::scaron => 0.389,
            self::scedilla => 0.389,
            self::iacute => 0.278,
            self::lozenge => 0.471,
            self::Rcaron => 0.667,
            self::Gcommaaccent => 0.722,
            self::ucircumflex => 0.500,
            self::acircumflex => 0.444,
            self::Amacron => 0.722,
            self::rcaron => 0.333,
            self::ccedilla => 0.444,
            self::Zdotaccent => 0.611,
            self::Thorn => 0.556,
            self::Omacron => 0.722,
            self::Racute => 0.667,
            self::Sacute => 0.556,
            self::dcaron => 0.588,
            self::Umacron => 0.722,
            self::uring => 0.500,
            self::threesuperior => 0.300,
            self::Ograve => 0.722,
            self::Agrave => 0.722,
            self::Abreve => 0.722,
            self::multiply => 0.564,
            self::uacute => 0.500,
            self::Tcaron => 0.611,
            self::partialdiff => 0.476,
            self::ydieresis => 0.500,
            self::Nacute => 0.722,
            self::icircumflex => 0.278,
            self::Ecircumflex => 0.611,
            self::adieresis => 0.444,
            self::edieresis => 0.444,
            self::cacute => 0.444,
            self::nacute => 0.500,
            self::umacron => 0.500,
            self::Ncaron => 0.722,
            self::Iacute => 0.333,
            self::plusminus => 0.564,
            self::brokenbar => 0.200,
            self::registered => 0.760,
            self::Gbreve => 0.722,
            self::Idotaccent => 0.333,
            self::summation => 0.600,
            self::Egrave => 0.611,
            self::racute => 0.333,
            self::omacron => 0.500,
            self::Zacute => 0.611,
            self::Zcaron => 0.611,
            self::greaterequal => 0.549,
            self::Eth => 0.722,
            self::Ccedilla => 0.667,
            self::lcommaaccent => 0.278,
            self::tcaron => 0.326,
            self::eogonek => 0.444,
            self::Uogonek => 0.722,
            self::Aacute => 0.722,
            self::Adieresis => 0.722,
            self::egrave => 0.444,
            self::zacute => 0.444,
            self::iogonek => 0.278,
            self::Oacute => 0.722,
            self::oacute => 0.500,
            self::amacron => 0.444,
            self::sacute => 0.389,
            self::idieresis => 0.278,
            self::Ocircumflex => 0.722,
            self::Ugrave => 0.722,
            self::Delta => 0.612,
            self::thorn => 0.500,
            self::twosuperior => 0.300,
            self::Odieresis => 0.722,
            self::mu => 0.500,
            self::igrave => 0.278,
            self::ohungarumlaut => 0.500,
            self::Eogonek => 0.611,
            self::dcroat => 0.500,
            self::threequarters => 0.750,
            self::Scedilla => 0.556,
            self::lcaron => 0.344,
            self::Kcommaaccent => 0.722,
            self::Lacute => 0.611,
            self::trademark => 0.980,
            self::edotaccent => 0.444,
            self::Igrave => 0.333,
            self::Imacron => 0.333,
            self::Lcaron => 0.611,
            self::onehalf => 0.750,
            self::lessequal => 0.549,
            self::ocircumflex => 0.500,
            self::ntilde => 0.500,
            self::Uhungarumlaut => 0.722,
            self::Eacute => 0.611,
            self::emacron => 0.444,
            self::gbreve => 0.500,
            self::onequarter => 0.750,
            self::Scaron => 0.556,
            self::Scommaaccent => 0.556,
            self::Ohungarumlaut => 0.722,
            self::degree => 0.400,
            self::ograve => 0.500,
            self::Ccaron => 0.667,
            self::ugrave => 0.500,
            self::radical => 0.453,
            self::Dcaron => 0.722,
            self::rcommaaccent => 0.333,
            self::Ntilde => 0.722,
            self::otilde => 0.500,
            self::Rcommaaccent => 0.667,
            self::Lcommaaccent => 0.611,
            self::Atilde => 0.722,
            self::Aogonek => 0.722,
            self::Aring => 0.722,
            self::Otilde => 0.722,
            self::zdotaccent => 0.444,
            self::Ecaron => 0.611,
            self::Iogonek => 0.333,
            self::kcommaaccent => 0.500,
            self::minus => 0.564,
            self::Icircumflex => 0.333,
            self::ncaron => 0.500,
            self::tcommaaccent => 0.278,
            self::logicalnot => 0.564,
            self::odieresis => 0.500,
            self::udieresis => 0.500,
            self::notequal => 0.549,
            self::gcommaaccent => 0.500,
            self::eth => 0.500,
            self::zcaron => 0.444,
            self::ncommaaccent => 0.500,
            self::onesuperior => 0.300,
            self::imacron => 0.278,
            self::Euro => 0.500,
        };
    }

    #[Override]
    public static function fromCharacterCode(int $code): ?self {
        return match ($code) {
            32 => self::space,
            33 => self::exclam,
            34 => self::quotedbl,
            35 => self::numbersign,
            36 => self::dollar,
            37 => self::percent,
            38 => self::ampersand,
            39 => self::quoteright,
            40 => self::parenleft,
            41 => self::parenright,
            42 => self::asterisk,
            43 => self::plus,
            44 => self::comma,
            45 => self::hyphen,
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
            64 => self::at,
            65 => self::A,
            66 => self::B,
            67 => self::C,
            68 => self::D,
            69 => self::E,
            70 => self::F,
            71 => self::G,
            72 => self::H,
            73 => self::I,
            74 => self::J,
            75 => self::K,
            76 => self::L,
            77 => self::M,
            78 => self::N,
            79 => self::O,
            80 => self::P,
            81 => self::Q,
            82 => self::R,
            83 => self::S,
            84 => self::T,
            85 => self::U,
            86 => self::V,
            87 => self::W,
            88 => self::X,
            89 => self::Y,
            90 => self::Z,
            91 => self::bracketleft,
            92 => self::backslash,
            93 => self::bracketright,
            94 => self::asciicircum,
            95 => self::underscore,
            96 => self::quoteleft,
            97 => self::a,
            98 => self::b,
            99 => self::c,
            100 => self::d,
            101 => self::e,
            102 => self::f,
            103 => self::g,
            104 => self::h,
            105 => self::i,
            106 => self::j,
            107 => self::k,
            108 => self::l,
            109 => self::m,
            110 => self::n,
            111 => self::o,
            112 => self::p,
            113 => self::q,
            114 => self::r,
            115 => self::s,
            116 => self::t,
            117 => self::u,
            118 => self::v,
            119 => self::w,
            120 => self::x,
            121 => self::y,
            122 => self::z,
            123 => self::braceleft,
            124 => self::bar,
            125 => self::braceright,
            126 => self::asciitilde,
            161 => self::exclamdown,
            162 => self::cent,
            163 => self::sterling,
            164 => self::fraction,
            165 => self::yen,
            166 => self::florin,
            167 => self::section,
            168 => self::currency,
            169 => self::quotesingle,
            170 => self::quotedblleft,
            171 => self::guillemotleft,
            172 => self::guilsinglleft,
            173 => self::guilsinglright,
            174 => self::fi,
            175 => self::fl,
            177 => self::endash,
            178 => self::dagger,
            179 => self::daggerdbl,
            180 => self::periodcentered,
            182 => self::paragraph,
            183 => self::bullet,
            184 => self::quotesinglbase,
            185 => self::quotedblbase,
            186 => self::quotedblright,
            187 => self::guillemotright,
            188 => self::ellipsis,
            189 => self::perthousand,
            191 => self::questiondown,
            193 => self::grave,
            194 => self::acute,
            195 => self::circumflex,
            196 => self::tilde,
            197 => self::macron,
            198 => self::breve,
            199 => self::dotaccent,
            200 => self::dieresis,
            202 => self::ring,
            203 => self::cedilla,
            205 => self::hungarumlaut,
            206 => self::ogonek,
            207 => self::caron,
            208 => self::emdash,
            225 => self::AE,
            227 => self::ordfeminine,
            232 => self::Lslash,
            233 => self::Oslash,
            234 => self::OE,
            235 => self::ordmasculine,
            241 => self::ae,
            245 => self::dotlessi,
            248 => self::lslash,
            249 => self::oslash,
            250 => self::oe,
            251 => self::germandbls,
            default => null,
        };
    }
}
