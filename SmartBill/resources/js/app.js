import './bootstrap';
import { 
    createIcons, 
    // Basic
    Menu, X, Bell, User, Settings, LogOut, ChevronRight, Search, Plus, 
    Filter, Download, Trash2, Edit, Check, Eye, Save, XCircle, MoreVertical, LayoutGrid, 
    List, Sliders, Calendar, Moon, Sun, Globe, Database, CreditCard, Box, Archive, 
    FileText, Image, RefreshCw, Layers, CheckCircle2, 
    // Business & SaaS
    Coins, Languages, SunMoon, ArrowRight, PanelTopOpen, ShieldCheck, 
    DatabaseZap, Loader2, SquarePen, FilterX, SearchCheck, SlidersHorizontal, 
    ChevronDown, FolderCog, FolderPlus, ArrowLeft, ImagePlus, ArchiveRestore, 
    PanelRightOpen, 
    // Additional & Missing
    ChartColumnBig, BriefcaseBusiness, Users, BadgeDollarSign, HelpCircle, 
    ScanLine, PanelsTopLeft, Settings2, ListChecks, Wallet, ArrowBigRightDash,
    Edit3, ChevronsUpDown, Zap, AlertCircle, CheckCircle,
    Inbox, CalendarDays, CalendarRange, CalendarCheck, ChevronUp
} from 'lucide';

const icons = {
    Menu, X, Bell, User, Settings, LogOut, ChevronRight, Search, Plus, 
    Filter, Download, Trash2, Edit, Check, Eye, Save, XCircle, MoreVertical, LayoutGrid, 
    List, Sliders, Calendar, Moon, Sun, Globe, Database, CreditCard, Box, Archive, 
    FileText, Image, RefreshCw, Layers, CheckCircle2, 
    Coins, Languages, SunMoon, ArrowRight, PanelTopOpen, ShieldCheck, 
    DatabaseZap, Loader2, SquarePen, FilterX, SearchCheck, SlidersHorizontal, 
    ChevronDown, FolderCog, FolderPlus, ArrowLeft, ImagePlus, ArchiveRestore, 
    PanelRightOpen,
    ChartColumnBig, BriefcaseBusiness, Users, BadgeDollarSign, HelpCircle, 
    ScanLine, PanelsTopLeft, Settings2, ListChecks, Wallet, ArrowBigRightDash,
    Edit3, ChevronsUpDown, Zap, AlertCircle, CheckCircle,
    Inbox, CalendarDays, CalendarRange, CalendarCheck, ChevronUp
};

// Unified Icon Initialization
const initializeIcons = () => {
    try {
        if (typeof createIcons === 'function') {
            createIcons({ icons });
        }
    } catch (e) {
        // Silently defer if not ready
    }
};

// Global access
window.initializeIcons = initializeIcons;
window.lucide = { createIcons, icons };

// Listeners
document.addEventListener('DOMContentLoaded', initializeIcons);
document.addEventListener('livewire:navigated', initializeIcons);
window.addEventListener('load', initializeIcons);

// Livewire 3+ Persistent Icons Hook
document.addEventListener('livewire:initialized', () => {
    Livewire.hook('morph.updated', (el, component) => {
        initializeIcons();
    });
});
