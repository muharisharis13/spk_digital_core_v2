#!/bin/bash

# Daftar direktori utama yang berisi proyek-proyek
PROJECT_DIRS=(
    "/var/www/spk-digital-core/aspacindo"
    "/var/www/spk-digital-core/crm"
    "/var/www/spk-digital-core/sjkm"
    "/var/www/spk-digital-core/yamahabrayan"
)

# Remote server details
REMOTE_USER="root"
REMOTE_HOST="103.76.121.42"

# Function to run commands on the remote server
run_remote_commands() {
    ssh -p 33445 $REMOTE_USER@$REMOTE_HOST << EOF
        # Iterasi melalui setiap direktori utama
        for BASE_DIR in "${PROJECT_DIRS[@]}"; do
            # Cek apakah direktori utama ada
            if [ -d "$BASE_DIR" ]; then
                # Iterasi melalui setiap folder di dalam BASE_DIR
                for dir in "$BASE_DIR"/*/; do
                    # Cek apakah folder tersebut adalah repository git
                    if [ -d "\$dir/.git" ]; then
                        echo "Pulling latest changes in \$dir"
                        # Pindah ke direktori project
                        cd "\$dir"
                        # Jalankan git pull origin production
                        git pull origin production
                        # Kembali ke direktori sebelumnya
                        cd - > /dev/null
                    else
                        echo "\$dir is not a git repository"
                    fi
                done
            else
                echo "\$BASE_DIR does not exist"
            fi
        done

       

        echo "Finished pulling all git repositories and reloading nginx"
EOF
}

# Jalankan fungsi untuk menjalankan perintah remote
run_remote_commands
