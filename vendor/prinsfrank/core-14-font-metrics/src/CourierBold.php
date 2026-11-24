<?php declare(strict_types=1);

namespace PrinsFrank\Core14FontMetrics;

use Override;

enum CourierBold implements Font {
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
            self::space => 0.600,
            self::exclam => 0.600,
            self::quotedbl => 0.600,
            self::numbersign => 0.600,
            self::dollar => 0.600,
            self::percent => 0.600,
            self::ampersand => 0.600,
            self::quoteright => 0.600,
            self::parenleft => 0.600,
            self::parenright => 0.600,
            self::asterisk => 0.600,
            self::plus => 0.600,
            self::comma => 0.600,
            self::hyphen => 0.600,
            self::period => 0.600,
            self::slash => 0.600,
            self::zero => 0.600,
            self::one => 0.600,
            self::two => 0.600,
            self::three => 0.600,
            self::four => 0.600,
            self::five => 0.600,
            self::six => 0.600,
            self::seven => 0.600,
            self::eight => 0.600,
            self::nine => 0.600,
            self::colon => 0.600,
            self::semicolon => 0.600,
            self::less => 0.600,
            self::equal => 0.600,
            self::greater => 0.600,
            self::question => 0.600,
            self::at => 0.600,
            self::A => 0.600,
            self::B => 0.600,
            self::C => 0.600,
            self::D => 0.600,
            self::E => 0.600,
            self::F => 0.600,
            self::G => 0.600,
            self::H => 0.600,
            self::I => 0.600,
            self::J => 0.600,
            self::K => 0.600,
            self::L => 0.600,
            self::M => 0.600,
            self::N => 0.600,
            self::O => 0.600,
            self::P => 0.600,
            self::Q => 0.600,
            self::R => 0.600,
            self::S => 0.600,
            self::T => 0.600,
            self::U => 0.600,
            self::V => 0.600,
            self::W => 0.600,
            self::X => 0.600,
            self::Y => 0.600,
            self::Z => 0.600,
            self::bracketleft => 0.600,
            self::backslash => 0.600,
            self::bracketright => 0.600,
            self::asciicircum => 0.600,
            self::underscore => 0.600,
            self::quoteleft => 0.600,
            self::a => 0.600,
            self::b => 0.600,
            self::c => 0.600,
            self::d => 0.600,
            self::e => 0.600,
            self::f => 0.600,
            self::g => 0.600,
            self::h => 0.600,
            self::i => 0.600,
            self::j => 0.600,
            self::k => 0.600,
            self::l => 0.600,
            self::m => 0.600,
            self::n => 0.600,
            self::o => 0.600,
            self::p => 0.600,
            self::q => 0.600,
            self::r => 0.600,
            self::s => 0.600,
            self::t => 0.600,
            self::u => 0.600,
            self::v => 0.600,
            self::w => 0.600,
            self::x => 0.600,
            self::y => 0.600,
            self::z => 0.600,
            self::braceleft => 0.600,
            self::bar => 0.600,
            self::braceright => 0.600,
            self::asciitilde => 0.600,
            self::exclamdown => 0.600,
            self::cent => 0.600,
            self::sterling => 0.600,
            self::fraction => 0.600,
            self::yen => 0.600,
            self::florin => 0.600,
            self::section => 0.600,
            self::currency => 0.600,
            self::quotesingle => 0.600,
            self::quotedblleft => 0.600,
            self::guillemotleft => 0.600,
            self::guilsinglleft => 0.600,
            self::guilsinglright => 0.600,
            self::fi => 0.600,
            self::fl => 0.600,
            self::endash => 0.600,
            self::dagger => 0.600,
            self::daggerdbl => 0.600,
            self::periodcentered => 0.600,
            self::paragraph => 0.600,
            self::bullet => 0.600,
            self::quotesinglbase => 0.600,
            self::quotedblbase => 0.600,
            self::quotedblright => 0.600,
            self::guillemotright => 0.600,
            self::ellipsis => 0.600,
            self::perthousand => 0.600,
            self::questiondown => 0.600,
            self::grave => 0.600,
            self::acute => 0.600,
            self::circumflex => 0.600,
            self::tilde => 0.600,
            self::macron => 0.600,
            self::breve => 0.600,
            self::dotaccent => 0.600,
            self::dieresis => 0.600,
            self::ring => 0.600,
            self::cedilla => 0.600,
            self::hungarumlaut => 0.600,
            self::ogonek => 0.600,
            self::caron => 0.600,
            self::emdash => 0.600,
            self::AE => 0.600,
            self::ordfeminine => 0.600,
            self::Lslash => 0.600,
            self::Oslash => 0.600,
            self::OE => 0.600,
            self::ordmasculine => 0.600,
            self::ae => 0.600,
            self::dotlessi => 0.600,
            self::lslash => 0.600,
            self::oslash => 0.600,
            self::oe => 0.600,
            self::germandbls => 0.600,
            self::Idieresis => 0.600,
            self::eacute => 0.600,
            self::abreve => 0.600,
            self::uhungarumlaut => 0.600,
            self::ecaron => 0.600,
            self::Ydieresis => 0.600,
            self::divide => 0.600,
            self::Yacute => 0.600,
            self::Acircumflex => 0.600,
            self::aacute => 0.600,
            self::Ucircumflex => 0.600,
            self::yacute => 0.600,
            self::scommaaccent => 0.600,
            self::ecircumflex => 0.600,
            self::Uring => 0.600,
            self::Udieresis => 0.600,
            self::aogonek => 0.600,
            self::Uacute => 0.600,
            self::uogonek => 0.600,
            self::Edieresis => 0.600,
            self::Dcroat => 0.600,
            self::commaaccent => 0.600,
            self::copyright => 0.600,
            self::Emacron => 0.600,
            self::ccaron => 0.600,
            self::aring => 0.600,
            self::Ncommaaccent => 0.600,
            self::lacute => 0.600,
            self::agrave => 0.600,
            self::Tcommaaccent => 0.600,
            self::Cacute => 0.600,
            self::atilde => 0.600,
            self::Edotaccent => 0.600,
            self::scaron => 0.600,
            self::scedilla => 0.600,
            self::iacute => 0.600,
            self::lozenge => 0.600,
            self::Rcaron => 0.600,
            self::Gcommaaccent => 0.600,
            self::ucircumflex => 0.600,
            self::acircumflex => 0.600,
            self::Amacron => 0.600,
            self::rcaron => 0.600,
            self::ccedilla => 0.600,
            self::Zdotaccent => 0.600,
            self::Thorn => 0.600,
            self::Omacron => 0.600,
            self::Racute => 0.600,
            self::Sacute => 0.600,
            self::dcaron => 0.600,
            self::Umacron => 0.600,
            self::uring => 0.600,
            self::threesuperior => 0.600,
            self::Ograve => 0.600,
            self::Agrave => 0.600,
            self::Abreve => 0.600,
            self::multiply => 0.600,
            self::uacute => 0.600,
            self::Tcaron => 0.600,
            self::partialdiff => 0.600,
            self::ydieresis => 0.600,
            self::Nacute => 0.600,
            self::icircumflex => 0.600,
            self::Ecircumflex => 0.600,
            self::adieresis => 0.600,
            self::edieresis => 0.600,
            self::cacute => 0.600,
            self::nacute => 0.600,
            self::umacron => 0.600,
            self::Ncaron => 0.600,
            self::Iacute => 0.600,
            self::plusminus => 0.600,
            self::brokenbar => 0.600,
            self::registered => 0.600,
            self::Gbreve => 0.600,
            self::Idotaccent => 0.600,
            self::summation => 0.600,
            self::Egrave => 0.600,
            self::racute => 0.600,
            self::omacron => 0.600,
            self::Zacute => 0.600,
            self::Zcaron => 0.600,
            self::greaterequal => 0.600,
            self::Eth => 0.600,
            self::Ccedilla => 0.600,
            self::lcommaaccent => 0.600,
            self::tcaron => 0.600,
            self::eogonek => 0.600,
            self::Uogonek => 0.600,
            self::Aacute => 0.600,
            self::Adieresis => 0.600,
            self::egrave => 0.600,
            self::zacute => 0.600,
            self::iogonek => 0.600,
            self::Oacute => 0.600,
            self::oacute => 0.600,
            self::amacron => 0.600,
            self::sacute => 0.600,
            self::idieresis => 0.600,
            self::Ocircumflex => 0.600,
            self::Ugrave => 0.600,
            self::Delta => 0.600,
            self::thorn => 0.600,
            self::twosuperior => 0.600,
            self::Odieresis => 0.600,
            self::mu => 0.600,
            self::igrave => 0.600,
            self::ohungarumlaut => 0.600,
            self::Eogonek => 0.600,
            self::dcroat => 0.600,
            self::threequarters => 0.600,
            self::Scedilla => 0.600,
            self::lcaron => 0.600,
            self::Kcommaaccent => 0.600,
            self::Lacute => 0.600,
            self::trademark => 0.600,
            self::edotaccent => 0.600,
            self::Igrave => 0.600,
            self::Imacron => 0.600,
            self::Lcaron => 0.600,
            self::onehalf => 0.600,
            self::lessequal => 0.600,
            self::ocircumflex => 0.600,
            self::ntilde => 0.600,
            self::Uhungarumlaut => 0.600,
            self::Eacute => 0.600,
            self::emacron => 0.600,
            self::gbreve => 0.600,
            self::onequarter => 0.600,
            self::Scaron => 0.600,
            self::Scommaaccent => 0.600,
            self::Ohungarumlaut => 0.600,
            self::degree => 0.600,
            self::ograve => 0.600,
            self::Ccaron => 0.600,
            self::ugrave => 0.600,
            self::radical => 0.600,
            self::Dcaron => 0.600,
            self::rcommaaccent => 0.600,
            self::Ntilde => 0.600,
            self::otilde => 0.600,
            self::Rcommaaccent => 0.600,
            self::Lcommaaccent => 0.600,
            self::Atilde => 0.600,
            self::Aogonek => 0.600,
            self::Aring => 0.600,
            self::Otilde => 0.600,
            self::zdotaccent => 0.600,
            self::Ecaron => 0.600,
            self::Iogonek => 0.600,
            self::kcommaaccent => 0.600,
            self::minus => 0.600,
            self::Icircumflex => 0.600,
            self::ncaron => 0.600,
            self::tcommaaccent => 0.600,
            self::logicalnot => 0.600,
            self::odieresis => 0.600,
            self::udieresis => 0.600,
            self::notequal => 0.600,
            self::gcommaaccent => 0.600,
            self::eth => 0.600,
            self::zcaron => 0.600,
            self::ncommaaccent => 0.600,
            self::onesuperior => 0.600,
            self::imacron => 0.600,
            self::Euro => 0.600,
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
