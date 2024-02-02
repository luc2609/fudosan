require('dotenv').config();
const { createServer } = require("http");
const { Server } = require("socket.io");
const Redis = require('ioredis');

const httpServer = createServer();
const io = new Server(httpServer, {
  transports: ["websocket", "polling"],
  cors: {
    origin: "*",
    allowedHeaders: "*",
  },
});

io.on('connection', function (socket) {
  console.log(`connect ${socket.id}`);
  socket.on('join', function (roomName) {
    if (!roomName) return;
    console.log(`user ${socket.id} join ${roomName}`);
    socket.join(roomName);
  })

  socket.on('leave', function (roomName) {
    if (!roomName) return;
    console.log(`user ${socket.id} leave ${roomName}`);
    socket.leave(roomName);
  })
});

io.on('error', function (socket) {
  console.log('error')
})

io.on('disconnect', function (socket) {
  console.log(`disconnect ${socket.id}`);
})

httpServer.listen(6001);

const redis = new Redis({
  port: process.env.REDIS_PORT || 6379,
  host: process.env.REDIS_HOST || "127.0.0.1",
  username: process.env.REDIS_USERNAME || "",
  password: process.env.REDIS_PASSWORD || "",
  db: process.env.REDIS_DATABASE || 0,
})
redis.psubscribe("*", function (error, count) { })
redis.on('pmessage', function (partner, channel, message) {
  console.log("channel--message", channel, message);
  const msg = JSON.parse(message)
  switch (channel) {
    case 'suns_company_database_contract-user-' + msg.data.companyId: {
      const prefix = 'suns_company_database_contract-user'
      io.to(prefix + '-' + msg.data.companyId).emit("message", {
        channel: prefix,
        data: msg.data.userContract,
      });
      break;
    }
    case 'suns_company_database_revenue-user-' + msg.data.companyId: {
      const prefix = 'suns_company_database_revenue-user'
      io.to(prefix + '-' + msg.data.companyId).emit("message", {
        channel: prefix,
        data: msg.data.userRevenue
      });
      break;
    }
    case 'suns_company_database_brokerage-user-' + msg.data.companyId: {
      const prefix = 'suns_company_database_brokerage-user';
      io.to(prefix + '-' + msg.data.companyId).emit("message", {
        channel: prefix,
        data: msg.data.userBrokerage,
      });
      break;
    }
    case 'suns_company_database_contract-division-' + msg.data.companyId: {
      const prefix = 'suns_company_database_contract-division';
      io.to(prefix + '-' + msg.data.companyId).emit("message", {
        channel: prefix,
        data: msg.data.contractDivision,
      });
      break;
    }
    case 'suns_company_database_revenue-division-' + msg.data.companyId: {
      const prefix = 'suns_company_database_revenue-division';
      io.to(prefix + '-' + msg.data.companyId).emit("message", {
        channel: prefix,
        data: msg.data.divisionRevenue,
      });
      break;
    }
    case 'suns_company_database_brokerage-division-' + msg.data.companyId: {
      const prefix = 'suns_company_database_brokerage-division';
      io.to(prefix + '-' + msg.data.companyId).emit("message", {
        channel: prefix,
        data: msg.data.divisionBrokerage,
      });
      break;
    }
    default:
      break;
  }
})