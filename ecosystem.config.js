module.exports = {
  apps : [{
    script    : "./server_node/server.js",
    instances : "1",
    exec_mode : "cluster"
  }]
}