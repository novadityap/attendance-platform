pipeline {
  agent any

  stages {
    stage('Checkout') {
      steps {
        cleanWs() 
        checkout scm
      }
    }

    stage('Build & Test') {
      steps {
        withCredentials([
          file(credentialsId: 'attendance-app-client', variable: 'CLIENT_ENV'),
          file(credentialsId: 'attendance-app-server', variable: 'SERVER_ENV'),
        ]) {
          sh '''
            cp "$CLIENT_ENV" client/.env 
            cp "$SERVER_ENV" server/.env 

            docker compose \
              -f docker-compose.test.yml \
              up \
              --build \
          '''
        }
      }
    }


    stage('Push Images') {
      steps {
        withCredentials([
          usernamePassword(
            credentialsId: 'docker-pat',
            usernameVariable: 'DOCKER_USER',
            passwordVariable: 'DOCKER_PASS',
          )
        ]) {
          sh '''
            echo "$DOCKER_PASS" | docker login -u "$DOCKER_USER" --password-stdin
            docker compose -f docker-compose.test.yml push
          '''
        }
      }
    }
  }

  // post {
  //   always {
  //     sh '''
  //       docker compose \
  //         -f docker-compose.test.yml \
  //         down \
  //         --remove-orphans || true
  //     '''
  //   }
  // }



  post {
    always {
      // 1. Kita ambil log Laravel dari dalam container server sebelum dimatikan
      // Kita gunakan 'docker compose exec' atau 'docker cp'
      sh '''
        echo "=== DEBUG: Menampilkan Laravel Log ==="
        docker compose -f docker-compose.test.yml logs server | tail -n 50
        
        # Opsi lain: Jika ingin melihat isi file log spesifik di dalam container
        docker compose -f docker-compose.test.yml exec -T server cat storage/logs/laravel.log || echo "File log tidak ditemukan"
        echo "======================================"
      '''

      // 2. Baru setelah itu kita bersihkan
      sh '''
        docker compose \
          -f docker-compose.test.yml \
          down \
          --remove-orphans || true
      '''
    }
  }
}
