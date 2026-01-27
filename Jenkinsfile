pipeline {
  agent any

  stages {
    stage('Checkout') {
      steps {
        cleanWs()
        checkout scm
      }
    }

    stage('Prepare ENV') {
      steps {
        withCredentials([
          file(credentialsId: 'attendance-app-client-ci', variable: 'CLIENT_ENV'),
          file(credentialsId: 'attendance-app-server-ci', variable: 'SERVER_ENV'),
        ]) {
          sh '''
            cp "$CLIENT_ENV" client/.env.ci
            cp "$SERVER_ENV" server/.env.ci
          '''
        }
      }
    }

    stage('Build & Test (CI)') {
      steps {
        sh '''
          docker compose \
            -f docker-compose.ci.yml \
            up \
            --build \
            --abort-on-container-exit \
            --exit-code-from server
        '''
      }
    }

    stage('Push Images') {
      when {
        branch 'main'
      }
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
            docker compose -f docker-compose.ci.yml push
          '''
        }
      }
    }
  }

  post {
    always {
      sh '''
        docker compose \
          -f docker-compose.ci.yml \
          down \
          --remove-orphans || true
      '''
      cleanWs()
    }
  }
}
