import type { Brief } from './brief';

export interface ArchitectureRule {
    required: string[];
    optional: string[];
    forbidden: string[];
    structure: Record<string, 'file' | 'dir'>;
    patterns: {
        type: 'file_contains' | 'dir_contains' | 'file_not_contains' | 'dir_not_contains';
        path: string;
        value: string;
    }[];
}

export interface HttpTest {
    name: string;
    method: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';
    url: string;
    headers?: { name: string; value: string }[];
    body?: any;
    expected: {
        status: number;
        json_contains?: string[];
    };
}

export interface BrowserStep {
    action: 'goto' | 'click';
    url?: string;
    selector?: string;
}

export interface BrowserTest {
    name: string;
    steps: BrowserStep[];
    expected: {
        title?: string;
        text?: string;
    };
}

export interface FunctionTest {
    name: string;
    input: any[];
    expected_output: any[];
}

export interface TestConfig {
    type: 'http' | 'browser' | 'function';
    timeout_seconds: number;
    retries: number;
    base_path?: string;
    language?: string;
    tests: HttpTest[] | BrowserTest[] | FunctionTest[];
}

export interface BriefVersion {
    id: number;
    brief_id: number;
    version: string;
    architecture_rules: ArchitectureRule;
    test_config: TestConfig;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    brief?: Brief;
}
