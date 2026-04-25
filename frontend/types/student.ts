import type { CorrectionDetail } from "./correction";

export interface DashboardStats {
    total_missions: number;
    completed_missions: number;
    total_belly: number;
    rank: string;
    success_rate: number;
}

export interface RecentActivity {
    id: string;
    type: 'submission' | 'correction';
    title: string;
    date: string;
    status: string;
    icon: string;
}

export interface DashboardData {
    stats: DashboardStats;
    skills_progress: CorrectionDetail[];
    recent_activity: RecentActivity[];
}
