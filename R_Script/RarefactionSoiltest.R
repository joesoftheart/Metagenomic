#Rscript file.R or R < scriptName.R --no-save/--save

data=read.table("final.tx.groups.rarefaction", header=T)
png('Fig1_rarefactionSoil_joe.png',width=8, height=6, units="in", res=300)
plot(x=data$numsampled, y=data$X2.soils1_1, type="l", col="blue", xlab="# sequences sampled", ylab="Observed OTUs", ylim=c(0,800))#, xlim=c(0,130000), ylim=c(0,900))
lines(x=data$numsampled, y=data$X2.soils2_1, type="l", col="red")
lines(x=data$numsampled, y=data$X2.soils3_1, type="l",col="green")
lines(x=data$numsampled, y=data$X2.soils4_1, type="l",col="orange")
legend('topleft',c('soils1_1','soils2_1','soils3_1','soils4_1'), pch=c(16,16,16,16),lty=c(1,1,1,1), col=c('blue','red', 'green','orange', ncol=1), cex=0.8)
dev.off()
