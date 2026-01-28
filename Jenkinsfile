pipeline {
  agent any

  stages {
    stage('Checkout') {
      steps {
        checkout scm
      }
    }

    stage('Build & Test') {
      steps {
        withCredentials([
          file(credentialsId: 'attendance-app-client-test', variable: 'CLIENT_ENV'),
          file(credentialsId: 'attendance-app-server-test', variable: 'SERVER_ENV'),
        ]) {
          sh '''
            cp "$CLIENT_ENV" client/.env 
            cp "$SERVER_ENV" server/.env 
            
            docker compose \
              -f docker-compose.test.yml \
              up \
              --build \
              --abort-on-container-exit \
              --exit-code-from server
          '''
        }
      }
    }


    stage('Push Images') {
      steps {
        withCredentials([
          usernamePassword(
            credentialsId: 'dockerhub',
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

  post {
    always {
      sh '''
        docker compose \
          -f docker-compose.test.yml \
          down \
          --remove-orphans || true
      '''
    }
  }
}
