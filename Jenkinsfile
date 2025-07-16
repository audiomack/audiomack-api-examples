#!/usr/bin/env groovy
import java.text.SimpleDateFormat
import groovy.json.*

class BuildImage {
    def environment
}

properties(
        [
        disableConcurrentBuilds(),
        buildDiscarder(
            logRotator(artifactDaysToKeepStr: '100',
            artifactNumToKeepStr: '100',
            daysToKeepStr: '100',
            numToKeepStr: '100'
            )
                ),
                [$class: 'RebuildSettings', autoRebuild: false, rebuildDisabled: false],
                pipelineTriggers([[$class: 'PeriodicFolderTrigger', interval: '2m']])
        ]
)

// GIT
def gitUrlRoot = "https://github.com/audiomack/audiomack-api-examples"
def gitUrl = "${gitUrlRoot}.git"
def gitCommitsUrl = "${gitUrlRoot}/commits"

// ECR
ecrRepositoryName = ''
awsRegion = 'us-east-1'
ecrAWSAccountIdMaster = '130635258951'
ecrAWSAccountIdProd = '704506075394'
ecrRegistryUrl = "${ecrAWSAccountIdProd}.dkr.ecr.${awsRegion}.amazonaws.com"
ecrRepositoryFQN = "${ecrRegistryUrl}/${ecrRepositoryName}"
projectList = ["php-auth"]


node('ubuntu-docker') {
    wrap([$class: 'BuildUser']) {
    	wrap([$class: 'MaskPasswordsBuildWrapper']) {
           wrap([$class: 'TimestamperBuildWrapper'] ) {
               wrap([$class: 'AnsiColorBuildWrapper', 'colorMapName': 'xterm']) {
                  step([$class: 'WsCleanup'])
                    stage('Clone repositories') {
                         this.checkoutSource(gitUrl)
			            }
                        }
                    for(item in projectList) {
                        stage('Build image and push to ECR for $item') {
                            def ecrRepositoryName = item
                            this.buildImage(ecrRepositoryName)
                            this.createRepo(ecrRepositoryName)
                            this.pushImages(ecrRepositoryName, imageVersion, ecrAWSAccountIdProd)
                            this.pushImages(ecrRepositoryName,  imageVersion, ecrAWSAccountIdMaster)
                            this.sendSlack(ecrRepositoryName, "${imageVersion}")
                            
                           }
                        }
                    }     
                }
            }
     
         }
def removeAutodeleteImages() {
    this.runDocker('image prune -a -f --filter "label=autodelete=true"')
    echo 'removed autodelete images'
}

def withDockerCleanup(f) {
    try {
        this.removeAutodeleteImages()
        f()
    } finally {
        this.removeAutodeleteImages()
        this.runDocker('images')
    }
}

def checkoutSource(gitUrl) {
    def scmVars = checkout([$class: 'GitSCM', branches: scm.branches, poll: true,  userRemoteConfigs: [[credentialsId: 'audiomack-machine-user', url: gitUrl]]])
    def fullCommitHash = scmVars.GIT_COMMIT
    def commitHash = fullCommitHash.take(12)
    echo "Using git commit: ${fullCommitHash}"
    dir('deployments') {
 	   git([url: "https://github.com/audiomack/deployments.git", branch: 'master', credentialsId: 'audiomack-machine-user'])
    }
    return commitHash
}

def runDocker(command) {
    sh("sudo docker ${command}")
}

def buildImage(ecrRepositoryName) {
        def describeECRImagesCmd = "aws ecr describe-images --region ${awsRegion} --registry-id ${ecrAWSAccountIdProd} --repository-name ${ecrRepositoryName} --output json --query 'sort_by(imageDetails,& imagePushedAt)[-1].imageTags[0]'"
        def findLastSemanticVerCmd = "jq . --raw-output |  sed 's/\"//g'"
        def incVersionCmd = 'perl -pe \'s/^((\\d+\\.)*)(\\d+)(.*)$/$1.($3+1).$4/e\''
        def fullCmd = "${describeECRImagesCmd} | ${findLastSemanticVerCmd} | ${incVersionCmd}"
        imageVersion = sh(returnStdout: true, script: fullCmd).trim()
        if (!imageVersion) {
            imageVersion = '1.0.0'
        }
        if (imageVersion == "[]..1") {
            imageVersion = '1.0.0'
        }
        echo 'Next Image Version: ' + imageVersion
        def gitHash=sh (returnStdout: true, script: "git rev-parse HEAD").trim()
        def dateFormat = new SimpleDateFormat("yyyy-MM-dd_HH_mm_ss")
        def date = new Date()
        def buildDate = (dateFormat.format(date)) 
 sh(" cd php-auth && sudo docker build  --label org.label-schema.build-date=${buildDate} --label org.label-schema.vendor=Audiomack --label org.label-schema.name=${ecrRepositoryName} --label org.label-schema.version=${imageVersion} --label org.label-schema.vcs-ref=${gitHash} -t ${ecrRepositoryName}:${imageVersion} --no-cache --pull --progress=plain -f php-auth/Dockerfile .")
}


def createRepo(ecrRepositoryName) {
    sh("export ANSIBLE_FORCE_COLOR=true && ansible-playbook -i deployments/shared-resources/ansible/hosts  deployments/shared-resources/ansible/create_repo.yml -e \"repository_name=${ecrRepositoryName}\"")
}


def sendSlack(ecrRepositoryName, version) {
    sh("export ANSIBLE_FORCE_COLOR=true && ansible-playbook -i deployments/shared-resources/ansible/hosts  deployments/shared-resources/ansible/slack_notification.yml -e \"repo_name=${ecrRepositoryName}\" -e \"type=docker\" -e \"version=${version}\" -e \"channel=#ci\"")
}


def pushImages(ecrRepositoryName, tag, account) {
    stage('Pushing image') {
        echo "Region: us-east-1"
        echo "Pushing Image to ${account}"
        env.account = "${account}"
        env.ecrRepositoryName = "${ecrRepositoryName}"
        env.tag = "${tag}"
        sh label: '', script: '''#!/usr/bin/env bash
                                 cred=$(aws sts assume-role --role-arn arn:aws:iam::${account}:role/JenkinsCrossAccountRole --role-session-name "devops-amicleaner")
                                 export AWS_ACCESS_KEY_ID=$(echo ${cred} | jq .Credentials.AccessKeyId | xargs)
                                 export AWS_SECRET_ACCESS_KEY=$(echo ${cred} | jq .Credentials.SecretAccessKey | xargs)
                                 export AWS_SESSION_TOKEN=$(echo ${cred} | jq .Credentials.SessionToken | xargs)
                                 export AWS_DEFAULT_REGION=\'us-east-1\'
                                 aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin \${account}.dkr.ecr.us-east-1.amazonaws.com/php-auth 
                                 docker tag \${ecrRepositoryName}:\${tag} \${account}.dkr.ecr.us-east-1.amazonaws.com/${ecrRepositoryName}:\${tag}
                                 docker push \${account}.dkr.ecr.us-east-1.amazonaws.com/\${ecrRepositoryName}:\${tag}'''
    }
}
