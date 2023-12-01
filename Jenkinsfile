pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/mrleloi/messenger/'
            }
        }
        stage('Build Docker Image') {
            steps {
                sh 'make build-staging'
            }
        }
        stage('Start Application') {
            steps {
                sh 'make start-staging'
            }
        }
    }
}