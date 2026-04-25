export interface Language {
    id: number;
    name: string;
    docker_image: string;
    compile_command: string | null;
    run_command: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}
