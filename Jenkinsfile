pipeline {
    agent any

    stages {
        stage('Checkout') {
            when {
                expression {
                    return repo == "baityapp/edu" && (branch_base == "staging" || branch_base == "master") && current_status == "closed" && merged == "true"
                }
            }
            steps {
                git branch: branch_base, credentialsId: '3a0bdd53-2830-4427-bc9a-2ef3222caf4b', url: 'https://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@github.com/mrleloi/messenger.git'
                sh "git pull origin ${branch_base}"
            }
        }
        stage('Build Staging') {
            when {
                expression {
                    return repo == "baityapp/edu" && (branch_base == "staging") && current_status == "closed" && merged == "true"
                }
            }
            steps {
                sh 'make build-staging'
            }
        }
        stage('Build Master') {
            when {
                expression {
                    return repo == "baityapp/edu" && (branch_base == "master") && current_status == "closed" && merged == "true"
                }
            }
            steps {
                sh 'make build-prod'
            }
        }
        stage('Start Staging') {
            when {
                expression {
                    return repo == "baityapp/edu" && (branch_base == "staging") && current_status == "closed" && merged == "true"
                }
            }
            steps {
                sh 'make start-staging'
            }
        }
        stage('Start Master') {
            when {
                expression {
                    return repo == "baityapp/edu" && (branch_base == "master") && current_status == "closed" && merged == "true"
                }
            }
            steps {
                sh 'make start-prod'
            }
        }
    }
}