pipeline {
  agent any

  options {
    timestamps()
  }

  stages {

    stage('Checkout') {
      steps {
        cleanWs()
        checkout scm
      }
    }

    stage('Copy .env Files') {
      steps {
        withCredentials([
          file(credentialsId: 'attendance-app-client', variable: 'CLIENT_ENV'),
          file(credentialsId: 'attendance-app-server', variable: 'SERVER_ENV'),
        ]) {
          sh '''
            cp "$CLIENT_ENV" client/.env.development
            cp "$SERVER_ENV" server/.env
          '''
        }
      }
    }

    stage('Build Images') {
      steps {
        sh '''
          docker compose -f docker-compose.ci.yml build
        '''
      }
    }

    stage('Start Containers') {
      steps {
        sh '''
          docker compose -f docker-compose.ci.yml up -d
        '''
      }
    }

    stage('Run Server Tests') {
      steps {
        sh '''
          until docker compose -f docker-compose.ci.yml exec -T postgres \
            sh -c "pg_isready -U postgres"; do
            sleep 1
          done

          docker compose -f docker-compose.ci.yml exec -T server sh -c "
            php artisan migrate:fresh --seed &&
            php artisan test
          "
        '''
      }
    }

    stage('Push Docker Images') {
      when {
        branch 'main'
      }
      steps {
        withCredentials([
          usernamePassword(
            credentialsId: 'dockerhub',
            usernameVariable: 'DOCKER_USER',
            passwordVariable: 'DOCKER_PASS',
          ),
        ]) {
          sh '''
            echo "$DOCKER_PASS" | docker login -u "$DOCKER_USER" --password-stdin
            docker compose -f docker-compose.ci.yml push
          '''
        }
      }
    }
  }

  post {
    always {
      sh '''
        docker compose -f docker-compose.ci.yml down \
          --volumes \
          --remove-orphans || true
      '''
      cleanWs()
    }
  }
}
