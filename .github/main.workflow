workflow "Lint" {
  on = "push"
  resolves = ["psalm"]
}

action "PHP-CS-Fixer" {
  uses = "docker://oskarstark/php-cs-fixer-ga"
  secrets = ["GITHUB_TOKEN"]
  args = "--diff --dry-run --allow-risky=yes"
}

action "psalm" {
  uses = "docker://mickaelandrieu/psalm-ga"
  needs = ["PHP-CS-Fixer"]
  secrets = ["GITHUB_TOKEN"]
  args = " --diff --diff-methods"
}
