workflow "Lint" {
  on = "push"
  resolves = ["phpunit"]
}

action "phpunit" {
  uses = "./.github/actions/phpunit"
  secrets = ["GITHUB_TOKEN"]
}
