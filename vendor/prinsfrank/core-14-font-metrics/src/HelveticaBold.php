<?php declare(strict_types=1);

namespace PrinsFrank\Core14FontMetrics;

use Override;

enum HelveticaBold implements Font {
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
            self::space => 0.278,
            self::exclam => 0.333,
            self::quotedbl => 0.474,
            self::numbersign => 0.556,
            self::dollar => 0.556,
            self::percent => 0.889,
            self::ampersand => 0.722,
            self::quoteright => 0.278,
            self::parenleft => 0.333,
            self::parenright => 0.333,
            self::asterisk => 0.389,
            self::plus => 0.584,
            self::comma => 0.278,
            self::hyphen => 0.333,
            self::period => 0.278,
            self::slash => 0.278,
            self::zero => 0.556,
            self::one => 0.556,
            self::two => 0.556,
            self::three => 0.556,
            self::four => 0.556,
            self::five => 0.556,
            self::six => 0.556,
            self::seven => 0.556,
            self::eight => 0.556,
            self::nine => 0.556,
            self::colon => 0.333,
            self::semicolon => 0.333,
            self::less => 0.584,
            self::equal => 0.584,
            self::greater => 0.584,
            self::question => 0.611,
            self::at => 0.975,
            self::A => 0.722,
            self::B => 0.722,
            self::C => 0.722,
            self::D => 0.722,
            self::E => 0.667,
            self::F => 0.611,
            self::G => 0.778,
            self::H => 0.722,
            self::I => 0.278,
            self::J => 0.556,
            self::K => 0.722,
            self::L => 0.611,
            self::M => 0.833,
            self::N => 0.722,
            self::O => 0.778,
            self::P => 0.667,
            self::Q => 0.778,
            self::R => 0.722,
            self::S => 0.667,
            self::T => 0.611,
            self::U => 0.722,
            self::V => 0.667,
            self::W => 0.944,
            self::X => 0.667,
            self::Y => 0.667,
            self::Z => 0.611,
            self::bracketleft => 0.333,
            self::backslash => 0.278,
            self::bracketright => 0.333,
            self::asciicircum => 0.584,
            self::underscore => 0.556,
            self::quoteleft => 0.278,
            self::a => 0.556,
            self::b => 0.611,
            self::c => 0.556,
            self::d => 0.611,
            self::e => 0.556,
            self::f => 0.333,
            self::g => 0.611,
            self::h => 0.611,
            self::i => 0.278,
            self::j => 0.278,
            self::k => 0.556,
            self::l => 0.278,
            self::m => 0.889,
            self::n => 0.611,
            self::o => 0.611,
            self::p => 0.611,
            self::q => 0.611,
            self::r => 0.389,
            self::s => 0.556,
            self::t => 0.333,
            self::u => 0.611,
            self::v => 0.556,
            self::w => 0.778,
            self::x => 0.556,
            self::y => 0.556,
            self::z => 0.500,
            self::braceleft => 0.389,
            self::bar => 0.280,
            self::braceright => 0.389,
            self::asciitilde => 0.584,
            self::exclamdown => 0.333,
            self::cent => 0.556,
            self::sterling => 0.556,
            self::fraction => 0.167,
            self::yen => 0.556,
            self::florin => 0.556,
            self::section => 0.556,
            self::currency => 0.556,
            self::quotesingle => 0.238,
            self::quotedblleft => 0.500,
            self::guillemotleft => 0.556,
            self::guilsinglleft => 0.333,
            self::guilsinglright => 0.333,
            self::fi => 0.611,
            self::fl => 0.611,
            self::endash => 0.556,
            self::dagger => 0.556,
            self::daggerdbl => 0.556,
            self::periodcentered => 0.278,
            self::paragraph => 0.556,
            self::bullet => 0.350,
            self::quotesinglbase => 0.278,
            self::quotedblbase => 0.500,
            self::quotedblright => 0.500,
            self::guillemotright => 0.556,
            self::ellipsis => 0.1000,
            self::perthousand => 0.1000,
            self::questiondown => 0.611,
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
            self::AE => 0.1000,
            self::ordfeminine => 0.370,
            self::Lslash => 0.611,
            self::Oslash => 0.778,
            self::OE => 0.1000,
            self::ordmasculine => 0.365,
            self::ae => 0.889,
            self::dotlessi => 0.278,
            self::lslash => 0.278,
            self::oslash => 0.611,
            self::oe => 0.944,
            self::germandbls => 0.611,
            self::Idieresis => 0.278,
            self::eacute => 0.556,
            self::abreve => 0.556,
            self::uhungarumlaut => 0.611,
            self::ecaron => 0.556,
            self::Ydieresis => 0.667,
            self::divide => 0.584,
            self::Yacute => 0.667,
            self::Acircumflex => 0.722,
            self::aacute => 0.556,
            self::Ucircumflex => 0.722,
            self::yacute => 0.556,
            self::scommaaccent => 0.556,
            self::ecircumflex => 0.556,
            self::Uring => 0.722,
            self::Udieresis => 0.722,
            self::aogonek => 0.556,
            self::Uacute => 0.722,
            self::uogonek => 0.611,
            self::Edieresis => 0.667,
            self::Dcroat => 0.722,
            self::commaaccent => 0.250,
            self::copyright => 0.737,
            self::Emacron => 0.667,
            self::ccaron => 0.556,
            self::aring => 0.556,
            self::Ncommaaccent => 0.722,
            self::lacute => 0.278,
            self::agrave => 0.556,
            self::Tcommaaccent => 0.611,
            self::Cacute => 0.722,
            self::atilde => 0.556,
            self::Edotaccent => 0.667,
            self::scaron => 0.556,
            self::scedilla => 0.556,
            self::iacute => 0.278,
            self::lozenge => 0.494,
            self::Rcaron => 0.722,
            self::Gcommaaccent => 0.778,
            self::ucircumflex => 0.611,
            self::acircumflex => 0.556,
            self::Amacron => 0.722,
            self::rcaron => 0.389,
            self::ccedilla => 0.556,
            self::Zdotaccent => 0.611,
            self::Thorn => 0.667,
            self::Omacron => 0.778,
            self::Racute => 0.722,
            self::Sacute => 0.667,
            self::dcaron => 0.743,
            self::Umacron => 0.722,
            self::uring => 0.611,
            self::threesuperior => 0.333,
            self::Ograve => 0.778,
            self::Agrave => 0.722,
            self::Abreve => 0.722,
            self::multiply => 0.584,
            self::uacute => 0.611,
            self::Tcaron => 0.611,
            self::partialdiff => 0.494,
            self::ydieresis => 0.556,
            self::Nacute => 0.722,
            self::icircumflex => 0.278,
            self::Ecircumflex => 0.667,
            self::adieresis => 0.556,
            self::edieresis => 0.556,
            self::cacute => 0.556,
            self::nacute => 0.611,
            self::umacron => 0.611,
            self::Ncaron => 0.722,
            self::Iacute => 0.278,
            self::plusminus => 0.584,
            self::brokenbar => 0.280,
            self::registered => 0.737,
            self::Gbreve => 0.778,
            self::Idotaccent => 0.278,
            self::summation => 0.600,
            self::Egrave => 0.667,
            self::racute => 0.389,
            self::omacron => 0.611,
            self::Zacute => 0.611,
            self::Zcaron => 0.611,
            self::greaterequal => 0.549,
            self::Eth => 0.722,
            self::Ccedilla => 0.722,
            self::lcommaaccent => 0.278,
            self::tcaron => 0.389,
            self::eogonek => 0.556,
            self::Uogonek => 0.722,
            self::Aacute => 0.722,
            self::Adieresis => 0.722,
            self::egrave => 0.556,
            self::zacute => 0.500,
            self::iogonek => 0.278,
            self::Oacute => 0.778,
            self::oacute => 0.611,
            self::amacron => 0.556,
            self::sacute => 0.556,
            self::idieresis => 0.278,
            self::Ocircumflex => 0.778,
            self::Ugrave => 0.722,
            self::Delta => 0.612,
            self::thorn => 0.611,
            self::twosuperior => 0.333,
            self::Odieresis => 0.778,
            self::mu => 0.611,
            self::igrave => 0.278,
            self::ohungarumlaut => 0.611,
            self::Eogonek => 0.667,
            self::dcroat => 0.611,
            self::threequarters => 0.834,
            self::Scedilla => 0.667,
            self::lcaron => 0.400,
            self::Kcommaaccent => 0.722,
            self::Lacute => 0.611,
            self::trademark => 0.1000,
            self::edotaccent => 0.556,
            self::Igrave => 0.278,
            self::Imacron => 0.278,
            self::Lcaron => 0.611,
            self::onehalf => 0.834,
            self::lessequal => 0.549,
            self::ocircumflex => 0.611,
            self::ntilde => 0.611,
            self::Uhungarumlaut => 0.722,
            self::Eacute => 0.667,
            self::emacron => 0.556,
            self::gbreve => 0.611,
            self::onequarter => 0.834,
            self::Scaron => 0.667,
            self::Scommaaccent => 0.667,
            self::Ohungarumlaut => 0.778,
            self::degree => 0.400,
            self::ograve => 0.611,
            self::Ccaron => 0.722,
            self::ugrave => 0.611,
            self::radical => 0.549,
            self::Dcaron => 0.722,
            self::rcommaaccent => 0.389,
            self::Ntilde => 0.722,
            self::otilde => 0.611,
            self::Rcommaaccent => 0.722,
            self::Lcommaaccent => 0.611,
            self::Atilde => 0.722,
            self::Aogonek => 0.722,
            self::Aring => 0.722,
            self::Otilde => 0.778,
            self::zdotaccent => 0.500,
            self::Ecaron => 0.667,
            self::Iogonek => 0.278,
            self::kcommaaccent => 0.556,
            self::minus => 0.584,
            self::Icircumflex => 0.278,
            self::ncaron => 0.611,
            self::tcommaaccent => 0.333,
            self::logicalnot => 0.584,
            self::odieresis => 0.611,
            self::udieresis => 0.611,
            self::notequal => 0.549,
            self::gcommaaccent => 0.611,
            self::eth => 0.611,
            self::zcaron => 0.500,
            self::ncommaaccent => 0.611,
            self::onesuperior => 0.333,
            self::imacron => 0.278,
            self::Euro => 0.556,
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
