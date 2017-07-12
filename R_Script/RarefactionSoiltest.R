args<-commandArgs(TRUE)
data=read.table(args[1], header=T)
png(args[2],width=8, height=6, units="in", res=300)



plot(x=data$numsampled, y=data$X2.S1_1_16s_S1, type="l", col="blue", xlab="# sequences sampled", ylab="Observed OTUs", ylim=c(0,800))#, xlim=c(0,130000), ylim=c(0,900))
lines(x=data$numsampled, y=data$X2.S2_1_16s_S3, type="l", col="red")
lines(x=data$numsampled, y=data$X2.S3_1_16s_S5, type="l",col="green")
lines(x=data$numsampled, y=data$X2.S4_1_16s_S7, type="l",col="orange")
legend('topleft',c(a,b,c,d), pch=c(16,16,16,16),lty=c(1,1,1,1), col=c('blue','red', 'green','orange', ncol=1), cex=0.8)
dev.off()
